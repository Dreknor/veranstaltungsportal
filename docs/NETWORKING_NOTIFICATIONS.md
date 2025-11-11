# Networking-Benachrichtigungen Implementierung

## Datum: 11. Januar 2025

## Implementierte Features

### 1. Vollständige Benachrichtigungen

#### A) ConnectionRequestNotification
**Wann:** Wenn jemand eine Verbindungsanfrage sendet
**An:** Den Empfänger der Anfrage
**Inhalt:**
- Titel: "Neue Verbindungsanfrage"
- Nachricht: "[Name] möchte sich mit Ihnen verbinden"
- Action: Link zum Profil des Absenders

#### B) ConnectionAcceptedNotification
**Wann:** Wenn eine Verbindungsanfrage akzeptiert wird
**An:** Den ursprünglichen Absender
**Inhalt:**
- Titel: "Verbindungsanfrage akzeptiert"
- Nachricht: "[Name] hat Ihre Verbindungsanfrage akzeptiert"
- Action: Link zum Profil

#### C) ConnectionDeclinedNotification ✨ NEU
**Wann:** Wenn eine Verbindungsanfrage abgelehnt wird
**An:** Den ursprünglichen Absender
**Inhalt:**
- Titel: "Verbindungsanfrage abgelehnt"
- Nachricht: "[Name] hat Ihre Verbindungsanfrage abgelehnt"
- Action: Link zum Profil

**Datei:** `app/Notifications/ConnectionDeclinedNotification.php`

### 2. Verbesserte Button-Beschriftungen

#### Vorher:
- ❌ "Akzeptieren" (unklar)
- ❌ "Ausstehend" (zu allgemein)
- ❌ Kein "Ablehnen"-Button

#### Nachher:
- ✅ "Zurückfolgen" (klar: gegenseitige Verbindung)
- ✅ "Anfrage gesendet" vs. "Möchte sich verbinden" (klar wer was gemacht hat)
- ✅ "Ablehnen"-Button vorhanden

### 3. Feldname-Konsolidierung

**Alt:** `allow_connections` (existierte nicht in DB)
**Neu:** `allow_networking` (konsolidiertes DSGVO-Feld)

**Mit Fallback:**
```php
$allowNetworking = isset($user->allow_networking) ? $user->allow_networking : true;
```

## Code-Änderungen

### ConnectionController.php

#### Import hinzugefügt:
```php
use App\Notifications\ConnectionDeclinedNotification;
```

#### decline() Methode erweitert:
```php
public function decline(Request $request, User $user): RedirectResponse
{
    $currentUser = $request->user();

    if ($currentUser->declineConnectionRequest($user)) {
        // NEU: Benachrichtigung senden
        $user->notify(new ConnectionDeclinedNotification($currentUser));
        
        return back()->with('success', 'Verbindungsanfrage abgelehnt.');
    }

    return back()->with('error', 'Verbindungsanfrage nicht gefunden.');
}
```

#### send() Methode aktualisiert:
```php
// Vorher:
if (!$user->allow_connections) { ... }

// Nachher:
$allowNetworking = isset($user->allow_networking) ? $user->allow_networking : true;
if (!$allowNetworking) { ... }
```

### users/show.blade.php

#### Button-Layout für eingehende Anfragen:
```blade
@else
    <!-- User received the request - can accept or decline -->
    <span class="px-4 py-2 border border-blue-500 bg-blue-50 rounded-md text-sm font-medium text-blue-700">
        Möchte sich verbinden
    </span>
    <form action="{{ route('connections.accept', $user) }}" method="POST" class="inline">
        @csrf
        <button type="submit" class="px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-green-600 hover:bg-green-700">
            Zurückfolgen
        </button>
    </form>
    <form action="{{ route('connections.decline', $user) }}" method="POST" class="inline">
        @csrf
        <button type="submit" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            Ablehnen
        </button>
    </form>
@endif
```

## User Flow

### Szenario 1: User A sendet Anfrage an User B

1. **User A** klickt "Verbinden" auf User B's Profil
2. **System** erstellt UserConnection mit `status='pending'`
3. **System** sendet `ConnectionRequestNotification` an User B ✉️
4. **User B** sieht Benachrichtigung in Bell-Icon
5. **User B** geht auf Profil von User A
6. **User B** sieht: "Möchte sich verbinden" + "Zurückfolgen" + "Ablehnen"

### Szenario 2: User B akzeptiert

7. **User B** klickt "Zurückfolgen"
8. **System** ändert `status='accepted'`
9. **System** sendet `ConnectionAcceptedNotification` an User A ✉️
10. **User A** sieht Benachrichtigung
11. **Beide** können nun Profile sehen + Kontaktdaten (wenn erlaubt)

### Szenario 3: User B lehnt ab

7. **User B** klickt "Ablehnen"
8. **System** löscht UserConnection
9. **System** sendet `ConnectionDeclinedNotification` an User A ✉️
10. **User A** sieht Benachrichtigung
11. **User A** kann neue Anfrage senden (wenn gewünscht)

## Button-Zustände

### Auf User-Profil (users/show.blade.php)

| Zustand | Anzeige | Aktionen |
|---------|---------|----------|
| Nicht verbunden | "Verbinden"-Button | Anfrage senden |
| Anfrage gesendet | "Anfrage gesendet" + "Zurückziehen" | Anfrage abbrechen |
| Anfrage empfangen | "Möchte sich verbinden" + "Zurückfolgen" + "Ablehnen" | Akzeptieren oder ablehnen |
| Verbunden | "Verbunden" ✓ + "Entfernen" | Verbindung auflösen |
| Networking deaktiviert | "Verbindungsanfragen deaktiviert" | Keine Aktion |

## Benachrichtigungs-Typen

Alle Benachrichtigungen verwenden:
- ✅ `implements ShouldQueue` - asynchrone Verarbeitung
- ✅ `via(['database'])` - gespeichert in notifications-Tabelle
- ✅ Vollständige Daten (Name, Foto, Link)
- ✅ Klickbar → führt zu relevanter Seite

## Testing

### Manueller Test-Plan

1. ✅ User A sendet Anfrage → User B bekommt Benachrichtigung
2. ✅ User B sieht "Zurückfolgen" und "Ablehnen"
3. ✅ User B klickt "Zurückfolgen" → User A bekommt Benachrichtigung
4. ✅ User B klickt "Ablehnen" → User A bekommt Benachrichtigung
5. ✅ Benachrichtigungen sind klickbar
6. ✅ Buttons funktionieren mit korrekten Routes
7. ✅ allow_networking wird respektiert

## Vorteile

✅ **Transparenz:** Beide Parteien werden über Status-Änderungen informiert
✅ **Klarheit:** "Zurückfolgen" ist verständlicher als "Akzeptieren"
✅ **Vollständigkeit:** Ablehnung sendet auch Benachrichtigung
✅ **Konsistenz:** Alle Networking-Aktionen haben Benachrichtigungen
✅ **UX:** Benutzer wissen immer, was passiert ist

## Status

✅ **Vollständig implementiert**
- Alle 3 Benachrichtigungstypen funktionieren
- Buttons haben klare Beschriftungen
- Ablehnen-Button verfügbar
- Feldnamen konsolidiert
- Rückwärtskompatibel

