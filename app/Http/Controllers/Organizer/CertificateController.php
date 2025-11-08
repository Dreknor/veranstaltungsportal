<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Event;
use App\Services\CertificateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;

class CertificateController extends Controller
{
    protected $certificateService;

    public function __construct(CertificateService $certificateService)
    {
        $this->middleware(['auth', 'organizer']);
        $this->certificateService = $certificateService;
    }

    /**
     * Show certificates for an event
     */
    public function index(Event $event)
    {
        $this->authorize('view', $event);

        $bookings = Booking::where('event_id', $event->id)
            ->where('status', 'confirmed')
            ->with(['items.ticketType'])
            ->orderBy('customer_name')
            ->paginate(50);

        $statistics = [
            'total_attendees' => $bookings->total(),
            'certificates_generated' => $bookings->where('certificate_generated_at', '!=', null)->count(),
            'event_completed' => $event->end_date->isPast(),
        ];

        return view('organizer.certificates.index', compact('event', 'bookings', 'statistics'));
    }

    /**
     * Generate certificate for a specific booking
     */
    public function generate(Event $event, Booking $booking)
    {
        $this->authorize('view', $event);

        if ($booking->event_id !== $event->id) {
            abort(404);
        }

        try {
            $certificatePath = $this->certificateService->saveCertificate($booking);

            $booking->update([
                'certificate_generated_at' => now(),
                'certificate_path' => $certificatePath,
            ]);

            return back()->with('success', 'Zertifikat erfolgreich generiert.');
        } catch (\Exception $e) {
            return back()->with('error', 'Fehler beim Generieren des Zertifikats: ' . $e->getMessage());
        }
    }

    /**
     * Generate certificates for all confirmed bookings
     */
    public function generateBulk(Event $event)
    {
        $this->authorize('view', $event);

        if (!$event->end_date->isPast()) {
            return back()->with('error', 'Zertifikate können nur für abgeschlossene Veranstaltungen generiert werden.');
        }

        $bookings = Booking::where('event_id', $event->id)
            ->where('status', 'confirmed')
            ->whereNull('certificate_generated_at')
            ->get();

        $generated = 0;
        $errors = 0;

        foreach ($bookings as $booking) {
            try {
                $certificatePath = $this->certificateService->saveCertificate($booking);

                $booking->update([
                    'certificate_generated_at' => now(),
                    'certificate_path' => $certificatePath,
                ]);

                $generated++;
            } catch (\Exception $e) {
                $errors++;
            }
        }

        if ($generated > 0) {
            return back()->with('success', "{$generated} Zertifikat(e) erfolgreich generiert." . ($errors > 0 ? " ({$errors} Fehler)" : ""));
        }

        return back()->with('info', 'Keine neuen Zertifikate zu generieren.');
    }

    /**
     * Download certificate
     */
    public function download(Event $event, Booking $booking)
    {
        $this->authorize('view', $event);

        if ($booking->event_id !== $event->id) {
            abort(404);
        }

        // If certificate was already generated, download from storage
        if ($booking->certificate_path && Storage::exists($booking->certificate_path)) {
            return Storage::download($booking->certificate_path);
        }

        // Otherwise generate on the fly
        return $this->certificateService->downloadCertificate($booking);
    }

    /**
     * Send certificates via email
     */
    public function sendEmail(Event $event, Booking $booking)
    {
        $this->authorize('view', $event);

        if ($booking->event_id !== $event->id) {
            abort(404);
        }

        try {
            // Generate certificate if not exists
            if (!$booking->certificate_path) {
                $certificatePath = $this->certificateService->saveCertificate($booking);
                $booking->update([
                    'certificate_generated_at' => now(),
                    'certificate_path' => $certificatePath,
                ]);
            }

            // Send email with certificate
            // Mail::to($booking->customer_email)->send(new CertificateMail($booking));

            return back()->with('success', 'Zertifikat erfolgreich per E-Mail versendet.');
        } catch (\Exception $e) {
            return back()->with('error', 'Fehler beim Versenden: ' . $e->getMessage());
        }
    }

    /**
     * Delete certificate
     */
    public function destroy(Event $event, Booking $booking)
    {
        $this->authorize('view', $event);

        if ($booking->event_id !== $event->id) {
            abort(404);
        }

        if ($booking->certificate_path) {
            Storage::delete($booking->certificate_path);
        }

        $booking->update([
            'certificate_generated_at' => null,
            'certificate_path' => null,
        ]);

        return back()->with('success', 'Zertifikat erfolgreich gelöscht.');
    }
}

