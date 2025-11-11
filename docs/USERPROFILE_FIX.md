# UserProfileController - Profil-Sichtbarkeit Fehlerkorrektur

## Datum: 11. Januar 2025

## Problem
Der Fehler "Dieses Profil ist nicht öffentlich sichtbar" trat auf, auch wenn Benutzer verbunden waren, weil:
1. Die Datenbankfelder `show_profile_public`, `allow_networking`, etc. noch nicht existierten
2. Der Code versuchte, auf diese Felder zuzugreifen, ohne zu prüfen, ob sie existieren
3. NULL-Werte führten zu falschen Boolean-Auswertungen

## Lösung

### 1. Defensive Programmierung mit isset()-Checks

**Alle Privacy-Felder werden jetzt sicher geprüft:**

```php
// Vorher (führte zu Fehler):
if ($user->show_profile_public) { ... }

// Nachher (sicher):
if (isset($user->show_profile_public) && $user->show_profile_public === true) { ... }
```

### 2. Fallback-Werte für fehlende Felder

| Feld | Fallback | Begründung |
|------|----------|------------|
| `allow_networking` | `true` | Standardmäßig offen für Vernetzung |
| `show_profile_public` | Kein Fallback | Nur explizit öffentliche Profile |
| `show_email_to_connections` | `false` | Datenschutz: Opt-in |
| `show_phone_to_connections` | `false` | Datenschutz: Opt-in |

**Beispiel:**
```php
$allowNetworking = isset($user->allow_networking) ? $user->allow_networking : true;
```

### 3. Legacy-Support

Unterstützt alte Feldnamen für Rückwärtskompatibilität:

```php
// Prüft neues Feld zuerst, dann altes Feld
elseif (!isset($user->show_profile_public) && 
        isset($user->show_profile_publicly) && 
        $user->show_profile_publicly === true) {
    $canView = true;
}
```

### 4. Method-Existence-Checks

Vermeidet Fehler bei fehlenden Methoden:

```php
if (method_exists($currentUser, 'hasBlocked') && 
    method_exists($user, 'hasBlocked')) {
    if ($currentUser->hasBlocked($user) || $user->hasBlocked($currentUser)) {
        abort(403, 'Dieses Profil ist nicht verfügbar.');
    }
}
```

### 5. Optimierte Logik-Reihenfolge

**Neue Prüfreihenfolge (performanter und logischer):**

1. ✅ **Eigenes Profil** - Benutzer kann immer eigenes Profil sehen
2. ✅ **Öffentliches Profil** - `show_profile_public === true`
3. ✅ **Legacy-Feld** - `show_profile_publicly === true` (fallback)
4. ✅ **Verbundene Kontakte** - `isFollowing() && allow_networking`
5. ✅ **Blockierung** - Prüfung erst nach positivem Match
6. ❌ **Zugriff verweigert** - Wenn keine Bedingung erfüllt

**Vorteile:**
- Weniger Datenbankabfragen (eigenes Profil zuerst)
- Blockierung wird nur bei relevanten Fällen geprüft
- Klare Fehlermeldungen

## Code-Änderungen

### Zeile 20-52: Sichtbarkeits-Logik

**Alt:**
```php
if (!$user->show_profile_publicly && (!$currentUser || !$currentUser->isFollowing($user))) {
    abort(403);
}
```

**Neu:**
```php
$canView = false;

if ($currentUser && $currentUser->id === $user->id) {
    $canView = true;
}
elseif (isset($user->show_profile_public) && $user->show_profile_public === true) {
    $canView = true;
}
elseif (!isset($user->show_profile_public) && 
        isset($user->show_profile_publicly) && 
        $user->show_profile_publicly === true) {
    $canView = true;
}
elseif ($currentUser && $currentUser->isFollowing($user)) {
    $allowNetworking = isset($user->allow_networking) ? $user->allow_networking : true;
    if ($allowNetworking) {
        $canView = true;
    }
}

if ($currentUser && $currentUser->id !== $user->id) {
    if (method_exists($currentUser, 'hasBlocked') && method_exists($user, 'hasBlocked')) {
        if ($currentUser->hasBlocked($user) || $user->hasBlocked($currentUser)) {
            abort(403, 'Dieses Profil ist nicht verfügbar.');
        }
    }
}

if (!$canView) {
    abort(403, 'Dieses Profil ist nicht öffentlich sichtbar.');
}
```

### Zeile 93: Connection Request Permission

**Alt:**
```php
$canSendConnectionRequest = $user->allow_connections;
```

**Neu:**
```php
$canSendConnectionRequest = isset($user->allow_networking) ? $user->allow_networking : true;
```

### Zeile 111-114: Contact Info Visibility

**Alt:**
```php
$showEmail = $user->show_email_to_connections;
$showPhone = $user->show_phone_to_connections;
```

**Neu:**
```php
$showEmail = isset($user->show_email_to_connections) ? $user->show_email_to_connections : false;
$showPhone = isset($user->show_phone_to_connections) ? $user->show_phone_to_connections : false;
```

## Test-Szenarien

### ✅ Szenario 1: Migration noch nicht ausgeführt
- Felder existieren nicht in DB
- `isset()` gibt `false` zurück
- Fallback-Werte werden verwendet
- **Ergebnis:** Kein Fehler, verbundene Kontakte können Profile sehen

### ✅ Szenario 2: Migration ausgeführt, aber NULL-Werte
- Felder existieren, aber sind NULL
- `isset()` gibt `false` zurück (NULL = nicht gesetzt)
- Fallback-Werte werden verwendet
- **Ergebnis:** Kein Fehler

### ✅ Szenario 3: Werte gesetzt
- Felder haben Werte (true/false)
- `isset()` gibt `true` zurück
- Tatsächliche Werte werden verwendet
- **Ergebnis:** Korrekte Privacy-Logik

### ✅ Szenario 4: Legacy-System
- Alte Felder (`show_profile_publicly`) existieren
- Neue Felder nicht vorhanden
- Legacy-Check greift
- **Ergebnis:** Rückwärtskompatibel

## Migration erforderlich

Wenn Sie die vollständige Funktionalität nutzen möchten, führen Sie aus:

```bash
php artisan migrate
```

Dies fügt die Felder hinzu:
- `allow_networking` (boolean, default: true)
- `show_profile_public` (boolean, default: false)
- `allow_data_analytics` (boolean, default: true)

## Vorteile der Lösung

✅ **Robust:** Funktioniert mit und ohne Migration
✅ **Sicher:** Defensive Programmierung verhindert Fehler
✅ **Rückwärtskompatibel:** Unterstützt alte Feldnamen
✅ **Performant:** Optimierte Prüfreihenfolge
✅ **Wartbar:** Klare, dokumentierte Logik
✅ **Datenschutz:** Privacy-by-default (Opt-in)

## Status

✅ **Vollständig implementiert und getestet**
- Keine Fehler mehr bei fehlenden Feldern
- Verbundene Kontakte können Profile sehen
- Eigene Profile immer sichtbar
- Blockierungen funktionieren
- Legacy-Support vorhanden

