@extends('layouts.eid')

@section('title', 'اصنع تهنئة عيد الأضحى باسم من تحب')
@section('description', 'أنشئ تهنئة عيد الأضحى باسم شخص عزيز وشاركها برابط خاص على فيسبوك.')

@section('content')
    <section class="eid-shell create-shell">
        <div class="hero-panel">
            <p class="eyebrow">عيد الأضحى المبارك</p>
            <h1>اصنع تهنئة عيد الأضحى خلال ثوانٍ</h1>
            <p class="hero-copy">اكتب اسم من تحب، واختر شكل التهنئة، وسننشئ لك رابط معايدة مميز يمكنك مشاركته بسهولة.</p>
            <div class="preview-card">
                <div class="mini-crescent"></div>
                <p>عيد أضحى مبارك يا <strong>أحمد</strong></p>
                <span>مثال: كل عام وأنت بخير يا أحمد، تقبل الله منا ومنكم صالح الأعمال.</span>
            </div>
        </div>

        <form class="eid-card form-card" method="POST" action="{{ route('eid.store') }}" id="wishForm" novalidate>
            @csrf
            <div class="form-grid">
                <label>
                    <span>اسم المرسل</span>
                    <input name="sender_name" value="{{ old('sender_name') }}" maxlength="50" required placeholder="مثال: محمد">
                    @error('sender_name')
                        <small>{{ $message }}</small>
                    @enderror
                </label>

                <label>
                    <span>اسم المستلم</span>
                    <input name="receiver_name" value="{{ old('receiver_name') }}" maxlength="50" required placeholder="مثال: أحمد">
                    @error('receiver_name')
                        <small>{{ $message }}</small>
                    @enderror
                </label>
            </div>

            <label>
                <span>صلة القرابة</span>
                <select name="relationship" required>
                    <option value="">اختر صلة القرابة</option>
                    @foreach ($relationships as $value => $label)
                        <option value="{{ $value }}" @selected(old('relationship') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('relationship')
                    <small>{{ $message }}</small>
                @enderror
            </label>

            <label>
                <span>أسلوب التهنئة</span>
                <select name="style" required>
                    <option value="">اختر الأسلوب</option>
                    @foreach ($styles as $value => $label)
                        <option value="{{ $value }}" @selected(old('style') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('style')
                    <small>{{ $message }}</small>
                @enderror
            </label>

            <label>
                <span>الصوت</span>
                <select name="audio_style">
                    @foreach ($audioStyles as $value => $label)
                        <option value="{{ $value }}" @selected(old('audio_style', 'none') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('audio_style')
                    <small>{{ $message }}</small>
                @enderror
            </label>

            <button class="gold-button" type="submit" data-loading-text="جاري إنشاء التهنئة...">
                اصنع التهنئة الآن
            </button>
        </form>
    </section>
@endsection

@push('scripts')
    <script>
        document.getElementById('wishForm')?.addEventListener('submit', function () {
            const button = this.querySelector('button[type="submit"]');
            if (! button) {
                return;
            }

            button.disabled = true;
            button.textContent = button.dataset.loadingText;
        });
    </script>
@endpush
