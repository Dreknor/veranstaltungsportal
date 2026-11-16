<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Rollenfarben nur noch aus der Palette: roles.color enthält künftig einen Tonschlüssel
 * (neutral, primary, success, warning, danger). Bestehende Hex-Werte werden dem nächsten Ton zugeordnet.
 */
return new class extends Migration
{
    private const TONES = ['neutral', 'primary', 'success', 'warning', 'danger'];

    public function up(): void
    {
        foreach (DB::table('roles')->select('id', 'color')->get() as $role) {
            $color = (string) $role->color;

            if (in_array($color, self::TONES, true)) {
                continue;
            }

            DB::table('roles')->where('id', $role->id)->update(['color' => $this->nearestTone($color)]);
        }
    }

    public function down(): void
    {
        // Die ursprünglichen Hex-Werte sind nicht rekonstruierbar; zurück auf den früheren Standard (Grau).
        DB::table('roles')->whereIn('color', self::TONES)->update(['color' => '#6b7280']);
    }

    /** Ton nach Farbton: Grautöne neutral, Rot/Rosa Gefahr, Orange/Gelb Warnung, Grün Erfolg, Blau/Türkis/Violett Petrol. */
    private function nearestTone(string $hex): string
    {
        $rgb = $this->rgb($hex);

        if ($rgb === null) {
            return 'neutral';
        }

        [$r, $g, $b] = array_map(fn (int $c) => $c / 255, $rgb);
        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $delta = $max - $min;

        if ($delta < 0.12) {
            return 'neutral';
        }

        $hue = match (true) {
            $max === $r => fmod((($g - $b) / $delta), 6),
            $max === $g => (($b - $r) / $delta) + 2,
            default => (($r - $g) / $delta) + 4,
        } * 60;
        $hue = fmod($hue + 360, 360);

        return match (true) {
            $hue < 20 || $hue >= 330 => 'danger',
            $hue < 70 => 'warning',
            $hue < 170 => 'success',
            default => 'primary',
        };
    }

    /** @return array{int, int, int}|null */
    private function rgb(string $hex): ?array
    {
        $hex = ltrim(trim($hex), '#');

        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }

        if (! preg_match('/^[0-9a-fA-F]{6}$/', $hex)) {
            return null;
        }

        return [hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2))];
    }
};
