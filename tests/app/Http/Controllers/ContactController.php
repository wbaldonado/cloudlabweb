<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessage;
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

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email:rfc,dns|max:255',
            'company' => 'nullable|string|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string',
        ]);

        $recipient = config('mail.from.address');
        
        if (empty($recipient)) {
            Log::error('Contact form email failed: MAIL_FROM_ADDRESS not configured');
            return redirect()
                ->route('contact.index')
                ->with('error', __('messages.contact.error'));
        }

        try {
            // Check if mail password is configured
            $mailPassword = config('mail.mailers.smtp.password');
            if (empty($mailPassword) || $mailPassword === 'null') {
                Log::error('Contact form email failed: MAIL_PASSWORD not configured in .env file');
                return redirect()
                    ->route('contact.index')
                    ->with('error', __('messages.contact.error') . ' (SMTP password not configured)');
            }

            Mail::to($recipient)->send(new ContactMessage($validated));
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
                'smtp_host' => config('mail.mailers.smtp.host'),
                'smtp_port' => config('mail.mailers.smtp.port'),
                'smtp_username' => config('mail.mailers.smtp.username'),
            ]);

            // Provide more specific error message
            $userMessage = __('messages.contact.error');
            if (str_contains($errorMessage, 'password') || str_contains($errorMessage, 'authentication')) {
                $userMessage .= ' (Verifica la contraseña SMTP)';
            } elseif (str_contains($errorMessage, 'connection') || str_contains($errorMessage, 'timeout')) {
                $userMessage .= ' (Error de conexión SMTP)';
            }

            return redirect()
                ->route('contact.index')
                ->with('error', $userMessage);
        }

        return redirect()
            ->route('contact.index')
            ->with('success', __('messages.contact.success'));
    }
}
