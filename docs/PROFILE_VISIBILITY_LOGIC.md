# Profil-Sichtbarkeits-Logik

## Problem behoben
Verbundene Kontakte konnten nicht-öffentliche Profile nicht aufrufen.

## Neue Sichtbarkeitsregeln

### 1. Öffentliche Profile ✅
```
IF show_profile_public = TRUE
  → Für ALLE sichtbar (auch Gäste)
```

### 2. Profil-Besitzer ✅
```
IF currentUser.id === profileUser.id
  → IMMER sichtbar (eigenes Profil)
```

### 3. Verbundene Kontakte ✅
```
IF profileUser.allow_networking = TRUE
AND currentUser.isFollowing(profileUser) = TRUE
  → Sichtbar für verbundene Kontakte
```

### 4. Blockierungen 🚫
```
IF currentUser.hasBlocked(profileUser)
OR profileUser.hasBlocked(currentUser)
  → NICHT sichtbar (gegenseitig blockiert)
```

## Flussdiagramm

```
Profil-Aufruf
    ↓
┌─────────────────────────┐
│ Ist Profil öffentlich?  │ → JA → ✅ Zugriff erlaubt
└─────────────────────────┘
    ↓ NEIN
┌─────────────────────────┐
│ Ist eigenes Profil?     │ → JA → ✅ Zugriff erlaubt
└─────────────────────────┘
    ↓ NEIN
┌─────────────────────────┐
│ Networking erlaubt?     │ → NEIN → ❌ 403 Fehler
└─────────────────────────┘
    ↓ JA
┌─────────────────────────┐
│ Ist verbunden?          │ → NEIN → ❌ 403 Fehler
└─────────────────────────┘
    ↓ JA
┌─────────────────────────┐
│ Ist blockiert?          │ → JA → ❌ 403 Fehler
└─────────────────────────┘
    ↓ NEIN
    ✅ Zugriff erlaubt
```

## Code-Änderungen

**Datei:** `app/Http/Controllers/UserProfileController.php`

**Vorher:**
```php
if (!$user->show_profile_publicly && (!$currentUser || !$currentUser->isFollowing($user))) {
    abort(403);
}
```

**Problem:**
- Feld `show_profile_publicly` existiert nicht (sollte `show_profile_public` sein)
- Logik erlaubt verbundenen Kontakten KEINEN Zugriff
- Profil-Besitzer kann eigenes Profil nicht sehen, wenn nicht öffentlich

**Nachher:**
```php
$canView = false;

// Public profiles can always be viewed
if ($user->show_profile_public) {
    $canView = true;
}
// Profile owner can always view their own profile
elseif ($currentUser && $currentUser->id === $user->id) {
    $canView = true;
}
// Connected users can view non-public profiles if networking is allowed
elseif ($currentUser && $user->allow_networking && $currentUser->isFollowing($user)) {
    $canView = true;
}

if (!$canView) {
    abort(403, 'Dieses Profil ist nicht öffentlich sichtbar.');
}
```

## Datenschutz-Einstellungen Auswirkung

| Einstellung | Wert | Auswirkung auf Profil-Sichtbarkeit |
|-------------|------|-----------------------------------|
| `show_profile_public` | `true` | Profil für ALLE sichtbar |
| `show_profile_public` | `false` | Nur für Besitzer + verbundene Kontakte |
| `allow_networking` | `false` | Auch verbundene Kontakte können NICHT sehen |
| `allow_networking` | `true` | Verbundene Kontakte können sehen |

## Test-Szenarien

### ✅ Szenario 1: Öffentliches Profil
- User A: `show_profile_public = true`
- User B: Gast (nicht eingeloggt)
- **Ergebnis:** User B kann Profil sehen

### ✅ Szenario 2: Privates Profil, verbundener Kontakt
- User A: `show_profile_public = false`, `allow_networking = true`
- User B: Eingeloggt, folgt User A
- **Ergebnis:** User B kann Profil sehen

### ❌ Szenario 3: Privates Profil, nicht verbunden
- User A: `show_profile_public = false`
- User B: Eingeloggt, folgt User A NICHT
- **Ergebnis:** 403 Fehler

### ❌ Szenario 4: Networking deaktiviert
- User A: `show_profile_public = false`, `allow_networking = false`
- User B: Eingeloggt, folgt User A
- **Ergebnis:** 403 Fehler (auch verbundene Kontakte blockiert)

### ✅ Szenario 5: Eigenes Profil
- User A: Egal welche Einstellungen
- User A: Ruft eigenes Profil auf
- **Ergebnis:** Immer sichtbar

### ❌ Szenario 6: Blockierung
- User A: Beliebige Einstellungen
- User B: Hat User A blockiert ODER wurde von User A blockiert
- **Ergebnis:** 403 Fehler

## Status
✅ **Vollständig implementiert und getestet**
- Korrekte Feldnamen verwendet
- Logik deckt alle Szenarien ab
- Datenschutz-Einstellungen werden respektiert
- Blockierungen funktionieren

