<?php

namespace App\Http\Controllers;

use App\Mail\RegistrationCancelledConfirmation;
use App\Models\UserRegistrationToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserRegistrationCancellationController extends Controller
{
    /**
     * Show confirmation page for cancelling registration
     */
    public function show(string $token)
    {
        $registrationToken = UserRegistrationToken::where('token', $token)->first();

        if (!$registrationToken) {
            return view('user.registration-cancel-error', [
                'message' => 'Ungültiger oder abgelaufener Link.',
            ]);
        }

        if ($registrationToken->isExpired()) {
            $registrationToken->delete();
            return view('user.registration-cancel-error', [
                'message' => 'Dieser Link ist abgelaufen. Bitte kontaktieren Sie den Organisator.',
            ]);
        }

        return view('user.registration-cancel-confirm', [
            'token' => $token,
            'email' => $registrationToken->email,
        ]);
    }

    /**
     * Process the cancellation
     */
    public function cancel(Request $request, string $token)
    {
        $registrationToken = UserRegistrationToken::where('token', $token)->first();

        if (!$registrationToken || $registrationToken->isExpired()) {
            return redirect()->route('home')->with('error', 'Ungültiger oder abgelaufener Link.');
        }

        $user = $registrationToken->user;
        $email = $user->email;

        // Delete the user (this will cascade delete the token)
        $user->delete();

        // Send confirmation email
        Mail::to($email)->send(new RegistrationCancelledConfirmation($email));

        return view('user.registration-cancelled-success', [
            'email' => $email,
        ]);
    }
}
