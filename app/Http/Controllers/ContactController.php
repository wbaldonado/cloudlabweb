<?php

namespace App\Http\Controllers;

use App\Mail\ContactConfirmation;
use App\Mail\ContactMessage;
use App\Support\ContactFormGuard;
use App\Support\LocalizedUrl;
use App\Support\Turnstile;
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
        $locale = $this->siteLocale($request);

        if ($request->filled('cl_hp')) {
            Log::info('Contact form skipped by honeypot');

            return $this->contactRedirect($request, $wantsJson, __('messages.contact.success'));
        }

        $this->cleanSubmittedFields($request);

        if ($reason = ContactFormGuard::rejection($request->input('form_token'))) {
            return $this->contactFailure($request, $wantsJson, __($reason));
        }

        if ($reason = Turnstile::rejection($request)) {
            return $this->contactFailure($request, $wantsJson, __($reason));
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email:rfc|max:255',
            'company' => 'required|string|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:5000',
        ], [
            'name.required' => __('messages.contact.name_required'),
            'email.required' => __('messages.contact.email_required'),
            'email.email' => __('messages.contact.email_invalid'),
            'company.required' => __('messages.contact.company_required'),
            'message.required' => __('messages.contact.message_required'),
        ]);

        if ($request->input('return_to') === 'home' && empty($validated['subject'])) {
            $validated['subject'] = __('messages.landing.form_subject');
        }
        if (empty($validated['subject'])) {
            $validated['subject'] = __('messages.contact.default_subject');
        }
        $validated['reference'] = $this->referenceNumber();

        $recipient = config('mail.contact_to', 'info@cloudlablatam.com');

        if (empty($recipient)) {
            Log::error('Contact form email failed: CONTACT_TO_EMAIL / mail.contact_to not configured');

            return $this->contactFailure($request, $wantsJson, __('messages.contact.error'));
        }

        try {
            $mailPassword = config('mail.mailers.smtp.password');
            if (empty($mailPassword) || $mailPassword === 'null') {
                Log::error('Contact form email failed: MAIL_PASSWORD not configured in .env file');

                return $this->contactFailure($request, $wantsJson, __('messages.contact.error'));
            }

            $this->deliverContactMessage($recipient, $validated, $locale);
            Log::info('Contact form email sent successfully', [
                'to' => $recipient,
                'from' => $validated['email'],
                'reference' => $validated['reference'],
                'locale' => $locale,
            ]);
            $this->deliverConfirmation($validated, $locale);
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

            return $this->contactFailure($request, $wantsJson, __('messages.contact.error'));
        }

        return $this->contactRedirect($request, $wantsJson, __('messages.contact.success_with_reference', [
            'reference' => $validated['reference'],
        ]));
    }

    /**
     * Send the notice from the brand mailbox. If Hostinger rejects it as spam
     * because Reply-To is an outside address, send once more without that header.
     */
    private function deliverContactMessage(string $recipient, array $validated, string $locale): void
    {
        try {
            Mail::to($recipient)->locale($locale)->send(new ContactMessage($validated));
        } catch (\Throwable $exception) {
            if (! $this->isSpamRejection($exception)) {
                throw $exception;
            }

            $fallback = new ContactMessage($validated);
            $fallback->includeReplyTo = false;
            Mail::to($recipient)->locale($locale)->send($fallback);

            Log::warning('Contact form email accepted only without Reply-To', [
                'to' => $recipient,
                'from' => $validated['email'] ?? 'unknown',
            ]);
        }
    }

    /**
     * Second message: confirmation to the visitor. A failure here does not
     * undo the internal notice or show an error, because the inquiry arrived.
     */
    private function deliverConfirmation(array $validated, string $locale): void
    {
        $address = $validated['email'] ?? '';
        if (! is_string($address) || ! filter_var($address, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        try {
            Mail::to($address)->locale($locale)->send(new ContactConfirmation($validated));
            Log::info('Contact confirmation sent', [
                'to' => $address,
                'reference' => $validated['reference'] ?? '',
                'locale' => $locale,
            ]);
        } catch (\Throwable $exception) {
            Log::warning('Contact confirmation failed', [
                'to' => $address,
                'reference' => $validated['reference'] ?? '',
                'error' => $exception->getMessage(),
            ]);
        }
    }

    /**
     * Language of the page that submitted the form: /es or /en.
     */
    private function siteLocale(Request $request): string
    {
        $locale = $request->route('locale');
        if (! is_string($locale) || ! LocalizedUrl::isLocale($locale)) {
            $locale = app()->getLocale();
        }
        if (! LocalizedUrl::isLocale($locale)) {
            $locale = 'es';
        }

        app()->setLocale($locale);

        return $locale;
    }

    private function referenceNumber(): string
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $code = '';
        $max = strlen($alphabet) - 1;
        for ($i = 0; $i < 6; $i++) {
            $code .= $alphabet[random_int(0, $max)];
        }

        return 'CL-'.now()->format('Ymd').'-'.$code;
    }

    private function cleanSubmittedFields(Request $request): void
    {
        $clean = [];
        foreach (['name', 'email', 'company', 'subject', 'message'] as $field) {
            if (! $request->exists($field)) {
                continue;
            }
            $value = $request->input($field);
            $clean[$field] = is_string($value) ? $this->cleanText($value) : '';
        }
        if ($clean !== []) {
            $request->merge($clean);
        }
    }

    private function cleanText(string $value): string
    {
        $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $value) ?? '';
        $value = preg_replace('/[\x{202A}-\x{202E}\x{2066}-\x{2069}]/u', '', $value) ?? '';

        return trim($value);
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
