<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWishRequest;
use App\Models\Wish;
use App\Services\WishAudioService;
use App\Services\WishMessageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

class EidWishController extends Controller
{
    public function create(): View
    {
        return view('eid.create', [
            'relationships' => StoreWishRequest::RELATIONSHIPS,
            'styles' => StoreWishRequest::STYLES,
            'audioStyles' => StoreWishRequest::AUDIO_STYLES,
        ]);
    }

    public function store(
        StoreWishRequest $request,
        WishMessageService $messageService,
        WishAudioService $audioService
    ): RedirectResponse {
        $validated = $request->validated();
        $audioStyle = $validated['audio_style'] ?? 'none';

        $wish = Wish::create([
            ...$validated,
            'audio_style' => $audioStyle,
            'message' => $messageService->generate(
                $validated['sender_name'],
                $validated['receiver_name'],
                $validated['relationship'],
                $validated['style'],
            ),
            'audio_path' => $audioService->resolve($audioStyle),
        ]);

        return redirect()->route('eid.show', $wish->code);
    }

    public function show(string $code, WishAudioService $audioService): View
    {
        $wish = Wish::where('code', $code)->firstOrFail();
        $wish->increment('views');

        if (! $wish->audio_path && $wish->audio_style && $wish->audio_style !== 'none') {
            $audioPath = $audioService->resolve($wish->audio_style);

            if ($audioPath) {
                $wish->forceFill(['audio_path' => $audioPath])->save();
            }
        }

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
}
