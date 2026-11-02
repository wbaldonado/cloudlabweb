<?php

namespace App\Http\Controllers;

use App\Models\CRM\CampaignRecipient;
use App\Support\CampaignMailer;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CrmTrackingController extends Controller
{
    public function open(string $token): Response
    {
        $recipient = CampaignRecipient::query()->where('token', $token)->first();
        if ($recipient && $recipient->status === 'sent') {
            CampaignMailer::recordOpen(
                $recipient,
                request()->ip(),
                request()->userAgent()
            );
        }

        // 1x1 transparent GIF
        $gif = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');

        return response($gif, 200, [
            'Content-Type' => 'image/gif',
            'Content-Length' => (string) strlen($gif),
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
        ]);
    }

    public function click(Request $request, string $token)
    {
        $recipient = CampaignRecipient::query()->with('campaign')->where('token', $token)->firstOrFail();
        $url = CampaignMailer::allowedClickUrl(
            CampaignMailer::decodeUrl((string) $request->query('u', '')),
            $recipient->campaign
        );
        if (! $url) {
            $url = url('/');
        }

        if ($recipient->status === 'sent' || $recipient->status === 'unsubscribed') {
            CampaignMailer::recordClick(
                $recipient,
                $url,
                $request->ip(),
                $request->userAgent()
            );
        }

        return redirect()->away($url);
    }

    public function unsubscribe(string $token)
    {
        $recipient = CampaignRecipient::query()->where('token', $token)->firstOrFail();
        CampaignMailer::unsubscribe($recipient, request()->ip(), request()->userAgent());

        return response()->view('emails.unsubscribed', [
            'email' => $recipient->email,
        ]);
    }
}
