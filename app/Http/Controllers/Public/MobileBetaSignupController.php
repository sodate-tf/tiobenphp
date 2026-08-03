<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\MobileBetaTester;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MobileBetaSignupController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('mobileBeta', [
            'google_email' => ['required', 'string', 'email', 'max:255'],
            'whatsapp' => ['required', 'string', 'max:30', 'regex:/^\+?[0-9\s\-\(\)]{10,20}$/'],
            'source_url' => ['nullable', 'string', 'max:500'],
        ]);

        $sourceUrl = trim((string) ($validated['source_url'] ?? ''));

        $tester = MobileBetaTester::firstOrNew([
            'google_email' => $validated['google_email'],
        ]);

        $wasAlreadySent = (bool) $tester->link_sent;

        $tester->fill([
            'whatsapp' => $validated['whatsapp'],
            'source_url' => $sourceUrl !== '' ? $sourceUrl : null,
        ]);

        if (!$wasAlreadySent) {
            $tester->link_sent = false;
            $tester->link_sent_at = null;
        }

        $tester->save();

        return back()
            ->with('mobile_beta_success', 'Em breve voce vai receber as informacoes para ser um testador.')
            ->with('mobile_beta_open', true);
    }
}