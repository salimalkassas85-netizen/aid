<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWishRequest;
use App\Models\Wish;
use App\Services\WishAudioService;
use App\Services\WishMessageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class EidWishController extends Controller
{
    public function create(): View
    {
        return view('eid.create', [
            'relationships' => StoreWishRequest::RELATIONSHIPS,
            'styles' => StoreWishRequest::STYLES,
        ]);
    }

    public function store(
        StoreWishRequest $request,
        WishMessageService $messageService,
        WishAudioService $audioService
    ): RedirectResponse {
        $validated = $request->validated();
        unset($validated['audio_recording']);

        $wish = Wish::create([
            ...$validated,
            'audio_style' => 'none',
            'message' => $messageService->generate(
                $validated['sender_name'],
                $validated['receiver_name'],
                $validated['relationship'],
                $validated['style'],
            ),
            'audio_path' => null,
        ]);

        if ($request->hasFile('audio_recording')) {
            $wish->forceFill([
                'audio_style' => 'recording',
                'audio_path' => $audioService->storeRecording($request->file('audio_recording'), $wish->code),
            ])->save();
        }

        return redirect()->route('eid.show', $wish->code);
    }

    public function show(string $code): View
    {
        $wish = Wish::where('code', $code)->firstOrFail();
        $wish->increment('views');

        return view('eid.show', [
            'wish' => $wish->fresh(),
            'greetingUrl' => route('eid.show', $wish->code),
        ]);
    }

    public function trackFacebookShare(string $code): Response
    {
        $wish = Wish::where('code', $code)->firstOrFail();
        $wish->increment('facebook_shares');

        return response()->noContent();
    }

    public function audio(string $code): BinaryFileResponse
    {
        $wish = Wish::where('code', $code)->firstOrFail();

        abort_if(! $wish->audio_path, 404);

        $audioRoot = realpath(public_path('audio'));
        $audioFile = realpath(public_path(ltrim($wish->audio_path, '/')));

        abort_if(! $audioRoot || ! $audioFile || ! str_starts_with($audioFile, $audioRoot), 404);

        return response()->file($audioFile, [
            'Content-Type' => mime_content_type($audioFile) ?: 'audio/wav',
            'Cache-Control' => 'public, max-age=604800',
        ]);
    }
}
