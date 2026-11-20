<?php

namespace App\Support;

use App\Models\EventReview;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * Kennzahlen einer Menge freigegebener Bewertungen (Veranstaltung, Angebot, Veranstalter).
 *
 * Einzige Stelle, an der ein Durchschnitt berechnet wird: Es zählen nur freigegebene Bewertungen.
 * Ein Durchschnitt wird erst ab MIN_COUNT Bewertungen gezeigt, die Anzahl steht immer dabei.
 */
final class ReviewSummary
{
    /** Ab so vielen freigegebenen Bewertungen wird ein Durchschnitt angezeigt. */
    public const MIN_COUNT = 3;

    /** Zeitraum (Monate) für die Gesamtbewertung eines Veranstalters. */
    public const ORGANIZATION_MONTHS = 24;

    /**
     * @param  array<int, int>  $distribution  Anzahl je Sternzahl 1–5
     */
    public function __construct(
        public readonly int $count,
        public readonly float $average,
        public readonly array $distribution = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0],
    ) {}

    public static function empty(): self
    {
        return new self(0, 0.0);
    }

    /**
     * @param  Builder<EventReview>|Relation<EventReview, *, *>  $query  Abfrage auf event_reviews
     */
    public static function fromQuery(Builder|Relation $query): self
    {
        $rows = (clone $query)
            ->where('event_reviews.is_approved', true)
            ->selectRaw('event_reviews.rating as stars, count(*) as total')
            ->groupBy('event_reviews.rating')
            ->toBase()
            ->get();

        $distribution = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        foreach ($rows as $row) {
            $stars = (int) $row->stars;
            if (isset($distribution[$stars])) {
                $distribution[$stars] += (int) $row->total;
            }
        }

        $count = array_sum($distribution);
        if ($count === 0) {
            return self::empty();
        }

        $sum = 0;
        foreach ($distribution as $stars => $total) {
            $sum += $stars * $total;
        }

        return new self($count, round($sum / $count, 1), $distribution);
    }

    public function hasReviews(): bool
    {
        return $this->count > 0;
    }

    /** Genug Bewertungen, um einen Durchschnitt zu zeigen. */
    public function isMeaningful(): bool
    {
        return $this->count >= self::MIN_COUNT;
    }

    /** „4,6“ */
    public function formattedAverage(): string
    {
        return number_format($this->average, 1, ',', '.');
    }

    /** „38 Bewertungen“ */
    public function countLabel(): string
    {
        return $this->count === 1 ? '1 Bewertung' : $this->count.' Bewertungen';
    }

    /** Anteil (0–100) einer Sternzahl für die Verteilung. */
    public function share(int $stars): int
    {
        if ($this->count === 0) {
            return 0;
        }

        return (int) round(($this->distribution[$stars] ?? 0) / $this->count * 100);
    }

    /**
     * @return array{count: int, average: float, distribution: array<int, int>}
     */
    public function toArray(): array
    {
        return ['count' => $this->count, 'average' => $this->average, 'distribution' => $this->distribution];
    }

    /**
     * @param  array{count: int, average: float, distribution: array<int, int>}  $data
     */
    public static function fromArray(array $data): self
    {
        return new self((int) $data['count'], (float) $data['average'], $data['distribution']);
    }
}
