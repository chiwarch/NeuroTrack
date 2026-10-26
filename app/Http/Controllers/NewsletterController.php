<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(
            ['email' => ['required', 'email:rfc', 'max:255']],
            [
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
            ]
        );

        NewsletterSubscriber::firstOrCreate(['email' => strtolower($data['email'])]);

        return redirect()->to(route('home').'#newsletter')
            ->with('newsletter_success', 'Terima kasih! Kamu sudah terdaftar di newsletter NeuroTrack.');
    }
}
