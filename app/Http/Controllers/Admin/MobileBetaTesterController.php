<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MobileBetaTester;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MobileBetaTesterController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $status = (string) $request->get('status', 'all');
        if (!in_array($status, ['all', 'pending', 'sent'], true)) {
            $status = 'all';
        }

        $testers = MobileBetaTester::query()
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($inner) use ($q) {
                    $inner->where('google_email', 'like', "%{$q}%")
                        ->orWhere('whatsapp', 'like', "%{$q}%");
                });
            })
            ->when($status === 'pending', fn ($query) => $query->where('link_sent', false))
            ->when($status === 'sent', fn ($query) => $query->where('link_sent', true))
            ->orderByDesc('created_at')
            ->paginate(30)
            ->withQueryString();

        return view('admin.mobile-beta-testers.index', [
            'testers' => $testers,
            'q' => $q,
            'status' => $status,
            'totalCount' => MobileBetaTester::count(),
            'pendingCount' => MobileBetaTester::where('link_sent', false)->count(),
            'sentCount' => MobileBetaTester::where('link_sent', true)->count(),
        ]);
    }

    public function markSent(MobileBetaTester $tester): RedirectResponse
    {
        if (!$tester->link_sent) {
            $tester->update([
                'link_sent' => true,
                'link_sent_at' => now(),
            ]);
        }

        return back()->with('success', 'Status do testador atualizado para link enviado.');
    }
}
