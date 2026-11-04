<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function index()
    {
        return view('contact.index')
            ->with('pageType', 'contact');
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $wantsJson = $request->expectsJson() || $request->ajax();

        if ($request->filled('website')) {
            return $this->contactRedirect($request, $wantsJson, __('messages.contact.success'));
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email:rfc,dns|max:255',
            'company' => 'nullable|string|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        if ($request->input('return_to') === 'home' && empty($validated['subject'])) {
            $validated['subject'] = __('messages.landing.form_subject');
        }

        $recipient = config('mail.contact_to', 'info@cloudlablatam.com');

        if (empty($recipient)) {
            Log::error('Contact form email failed: CONTACT_TO_EMAIL / mail.contact_to not configured');

            return $this->contactFailure($request, $wantsJson, __('messages.contact.error'));
        }

        try {
            $mailPassword = config('mail.mailers.smtp.password');
            if (empty($mailPassword) || $mailPassword === 'null') {
                Log::error('Contact form email failed: MAIL_PASSWORD not configured in .env file');

                return $this->contactFailure(
                    $request,
                    $wantsJson,
                    __('messages.contact.error') . ' (SMTP password not configured)'
                );
            }

            $this->deliverContactMessage($recipient, $validated);
            Log::info('Contact form email sent successfully', [
                'to' => $recipient,
                'from' => $validated['email'],
            ]);
        } catch (\Throwable $exception) {
            $errorMessage = $exception->getMessage();
            Log::error('Failed to send contact form email', [
                'error' => $errorMessage,
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'to' => $recipient,
                'from' => $validated['email'] ?? 'unknown',
                'subject' => $validated['subject'] ?? '',
                'smtp_host' => config('mail.mailers.smtp.host'),
                'smtp_port' => config('mail.mailers.smtp.port'),
                'smtp_username' => config('mail.mailers.smtp.username'),
            ]);

            $userMessage = __('messages.contact.error');
            if (str_contains($errorMessage, 'password') || str_contains($errorMessage, 'authentication')) {
                $userMessage .= ' (Verifica la contraseña SMTP)';
            } elseif (str_contains($errorMessage, 'connection') || str_contains($errorMessage, 'timeout')) {
                $userMessage .= ' (Error de conexión SMTP)';
            }

            return $this->contactFailure($request, $wantsJson, $userMessage);
        }

        return $this->contactRedirect($request, $wantsJson, __('messages.contact.success'));
    }

    /**
     * Send the notice from the brand mailbox. If Hostinger rejects it as spam
     * because Reply-To is an outside address, send once more without that header.
     */
    private function deliverContactMessage(string $recipient, array $validated): void
    {
        try {
            Mail::to($recipient)->send(new ContactMessage($validated));
        } catch (\Throwable $exception) {
            if (! $this->isSpamRejection($exception)) {
                throw $exception;
            }

            $fallback = new ContactMessage($validated);
            $fallback->includeReplyTo = false;
            Mail::to($recipient)->send($fallback);

            Log::warning('Contact form email accepted only without Reply-To', [
                'to' => $recipient,
                'from' => $validated['email'] ?? 'unknown',
            ]);
        }
    }

    private function isSpamRejection(\Throwable $exception): bool
    {
        $message = $exception->getMessage();

        return str_contains($message, '554') || str_contains($message, 'Spam message rejected');
    }

    private function contactRedirect(Request $request, bool $wantsJson, string $message): RedirectResponse|JsonResponse
    {
        if ($wantsJson) {
            return response()->json(['ok' => true, 'message' => $message]);
        }

        $target = $request->input('return_to') === 'home'
            ? route('home') . '#contacto'
            : route('contact.index');

        return redirect()->to($target)->with('success', $message);
    }

    private function contactFailure(Request $request, bool $wantsJson, string $message): RedirectResponse|JsonResponse
    {
        if ($wantsJson) {
            return response()->json(['ok' => false, 'message' => $message], 422);
        }

        $target = $request->input('return_to') === 'home'
            ? route('home') . '#contacto'
            : route('contact.index');

        return redirect()->to($target)->with('error', $message);
    }
}
