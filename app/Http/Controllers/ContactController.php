<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    /**
     * Zeige das Kontaktformular an
     */
    public function show()
    {
        return view('contact');
    }

    /**
     * Verarbeite die Kontaktanfrage
     */
    public function store(Request $request)
    {
        // Validiere die Eingaben
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10|max:5000',
            'g-recaptcha-response' => 'required'
        ], [
            'name.required' => 'Der Name ist erforderlich.',
            'name.string' => 'Der Name muss ein Text sein.',
            'name.max' => 'Der Name darf maximal 255 Zeichen lang sein.',
            'email.required' => 'Die E-Mail-Adresse ist erforderlich.',
            'email.email' => 'Bitte geben Sie eine gültige E-Mail-Adresse ein.',
            'email.max' => 'Die E-Mail-Adresse darf maximal 255 Zeichen lang sein.',
            'subject.required' => 'Das Betreff ist erforderlich.',
            'subject.string' => 'Das Betreff muss ein Text sein.',
            'subject.max' => 'Das Betreff darf maximal 255 Zeichen lang sein.',
            'message.required' => 'Die Nachricht ist erforderlich.',
            'message.string' => 'Die Nachricht muss ein Text sein.',
            'message.min' => 'Die Nachricht muss mindestens 10 Zeichen lang sein.',
            'message.max' => 'Die Nachricht darf maximal 5000 Zeichen lang sein.',
            'g-recaptcha-response.required' => 'Bitte bestätigen Sie, dass Sie kein Bot sind.',
        ]);

        // Sende E-Mail an den Administrator/Support
        try {
            Mail::raw(
                "Name: {$validated['name']}\n" .
                "E-Mail: {$validated['email']}\n\n" .
                "Nachricht:\n{$validated['message']}",
                function ($mail) use ($validated) {
                    $mail->to(config('mail.from.address'))
                         ->subject("[Kontaktformular] " . $validated['subject'])
                         ->replyTo($validated['email']);
                }
            );

            // Sende Bestätigungsmail an den Absender
            Mail::raw(
                "Hallo {$validated['name']},\n\n" .
                "vielen Dank für Ihre Nachricht. Wir haben Ihre Anfrage erhalten und werden " .
                "uns so schnell wie möglich bei Ihnen melden.\n\n" .
                "Mit freundlichen Grüßen,\n" .
                config('app.name'),
                function ($mail) use ($validated) {
                    $mail->to($validated['email'])
                         ->subject("Bestätigung - Ihre Kontaktanfrage")
                         ->from(config('mail.from.address'), config('app.name'));
                }
            );

            return back()->with('success', 'Danke für Ihre Nachricht! Wir werden uns in Kürze bei Ihnen melden.');
        } catch (\Exception $e) {
            \Log::error('Kontaktformular Fehler: ' . $e->getMessage());
            return back()->with('error', 'Es gab einen Fehler beim Versenden Ihrer Nachricht. Bitte versuchen Sie es später erneut.');
        }
    }
}

