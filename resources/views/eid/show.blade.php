@extends('layouts.eid')

@php
    $shareUrl = 'https://www.facebook.com/sharer/sharer.php?u='.rawurlencode($greetingUrl);
    $description = \Illuminate\Support\Str::limit($wish->message, 120, '');
    $ogImagePath = public_path('images/eid-og.jpg');
    $audioUrl = $wish->audio_path ? route('eid.audio', $wish->code) : null;
@endphp

@section('title', 'تهنئة عيد الأضحى إلى '.$wish->receiver_name)
@section('description', $description)

@section('meta')
    <meta property="og:title" content="تهنئة عيد الأضحى إلى {{ $wish->receiver_name }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ $greetingUrl }}">
    <meta property="og:type" content="website">
    @if (file_exists($ogImagePath))
        <meta property="og:image" content="{{ asset('images/eid-og.jpg') }}">
    @endif
@endsection

@section('content')
    <section class="eid-shell wish-shell">
        <div class="confetti" aria-hidden="true">
            @for ($i = 0; $i < 18; $i++)
                <span style="--i: {{ $i }}"></span>
            @endfor
        </div>

        <article class="eid-card greeting-card">
            <p class="eyebrow">رسالة عيد خاصة</p>
            <h1>عيد أضحى مبارك يا <span>{{ $wish->receiver_name }}</span></h1>
            <p class="message">{{ $wish->message }}</p>
            <p class="sender">مع أطيب التهاني من {{ $wish->sender_name }}</p>

            @if ($wish->audio_path)
                <div class="audio-box">
                    <span class="audio-pulse"></span>
                    <div>
                        <strong>استمع إلى التهنئة</strong>
                        <audio id="wishAudio" controls autoplay loop playsinline preload="auto" src="{{ $audioUrl }}"></audio>
                        <button class="audio-start-button" type="button" id="startAudio" hidden>تشغيل الصوت</button>
                        <a class="audio-direct-link" href="{{ $audioUrl }}" target="_blank" rel="noopener">فتح الصوت مباشرة</a>
                    </div>
                </div>
            @endif

            <div class="actions">
                <a class="gold-button facebook-share" href="{{ $shareUrl }}" target="_blank" rel="noopener" data-track-url="{{ route('eid.facebook-share', $wish->code) }}">
                    شارك التهنئة على فيسبوك
                </a>
                <button class="ghost-button" type="button" id="copyLink" data-url="{{ $greetingUrl }}">انسخ رابط التهنئة</button>
            </div>

            <a class="cta-link" href="{{ route('eid.create') }}">أعجبتك التهنئة؟ اصنع تهنئتك الآن</a>
        </article>
    </section>

    <div class="toast" id="copyToast" role="status" aria-live="polite">تم نسخ الرابط بنجاح</div>
@endsection

@push('scripts')
    <script>
        const copyButton = document.getElementById('copyLink');
        const toast = document.getElementById('copyToast');

        copyButton?.addEventListener('click', async function () {
            try {
                await navigator.clipboard.writeText(this.dataset.url);
                toast?.classList.add('show');
                window.setTimeout(() => toast?.classList.remove('show'), 2200);
            } catch (error) {
                window.prompt('انسخ الرابط:', this.dataset.url);
            }
        });

        document.querySelector('.facebook-share')?.addEventListener('click', function (event) {
            event.preventDefault();

            const shareUrl = this.href;
            const token = document.querySelector('meta[name="csrf-token"]')?.content;

            fetch(this.dataset.trackUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                },
            }).finally(() => {
                window.open(shareUrl, '_blank', 'noopener');
            });
        });

        const wishAudio = document.getElementById('wishAudio');
        const startAudio = document.getElementById('startAudio');

        if (wishAudio) {
            wishAudio.loop = true;

            window.addEventListener('load', async function () {
                try {
                    await wishAudio.play();
                } catch (error) {
                    startAudio.hidden = false;
                }
            });

            startAudio?.addEventListener('click', async function () {
                try {
                    await wishAudio.play();
                    this.hidden = true;
                } catch (error) {
                    this.textContent = 'اضغط تشغيل من مشغل الصوت';
                }
            });
        }
    </script>
@endpush
