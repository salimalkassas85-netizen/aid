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

        <form class="eid-card form-card" method="POST" action="{{ route('eid.store') }}" id="wishForm" enctype="multipart/form-data" novalidate>
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

            <div class="recorder-box">
                <div>
                    <span class="recorder-title">رسالة صوتية بصوتك</span>
                    <p>سجل تهنئة قصيرة حتى 10 ثواني، وسيستمع لها المستلم من نفس الصفحة.</p>
                </div>

                <input type="file" name="audio_recording" id="audioRecording" accept="audio/*" hidden>

                <div class="recorder-controls">
                    <button class="ghost-button" type="button" id="startRecording">ابدأ التسجيل</button>
                    <button class="ghost-button danger-button" type="button" id="stopRecording" disabled>إيقاف</button>
                    <span class="recording-time" id="recordingTime">00:00</span>
                </div>

                <audio class="recording-preview" id="recordingPreview" controls preload="none" hidden></audio>
                <p class="recording-status" id="recordingStatus">لن يتم تشغيل الصوت تلقائيًا عند المستلم.</p>

                @error('audio_recording')
                    <small>{{ $message }}</small>
                @enderror
            </div>

            <button class="gold-button" type="submit" data-loading-text="جاري إنشاء التهنئة...">
                اصنع التهنئة الآن
            </button>
        </form>
    </section>
@endsection

@push('scripts')
    <script>
        const wishForm = document.getElementById('wishForm');
        const startRecording = document.getElementById('startRecording');
        const stopRecording = document.getElementById('stopRecording');
        const recordingTime = document.getElementById('recordingTime');
        const recordingStatus = document.getElementById('recordingStatus');
        const recordingPreview = document.getElementById('recordingPreview');
        const audioRecording = document.getElementById('audioRecording');
        let mediaRecorder = null;
        let recordingStream = null;
        let recordingChunks = [];
        let recordingSeconds = 0;
        let recordingTimer = null;

        function setRecordingTime(seconds) {
            recordingTime.textContent = `00:${String(seconds).padStart(2, '0')}`;
        }

        function finishRecording() {
            if (! mediaRecorder || mediaRecorder.state === 'inactive') {
                return;
            }

            mediaRecorder.stop();
        }

        startRecording?.addEventListener('click', async function () {
            if (! navigator.mediaDevices || ! window.MediaRecorder) {
                recordingStatus.textContent = 'المتصفح لا يدعم التسجيل الصوتي.';
                return;
            }

            try {
                recordingStream = await navigator.mediaDevices.getUserMedia({ audio: true });
                recordingChunks = [];
                recordingSeconds = 0;
                setRecordingTime(recordingSeconds);

                mediaRecorder = new MediaRecorder(recordingStream);

                mediaRecorder.addEventListener('dataavailable', function (event) {
                    if (event.data.size > 0) {
                        recordingChunks.push(event.data);
                    }
                });

                mediaRecorder.addEventListener('stop', function () {
                    window.clearInterval(recordingTimer);
                    recordingStream?.getTracks().forEach((track) => track.stop());

                    const type = recordingChunks[0]?.type || 'audio/webm';
                    const blob = new Blob(recordingChunks, { type });
                    const file = new File([blob], 'eid-recording.webm', { type });
                    const files = new DataTransfer();
                    files.items.add(file);
                    audioRecording.files = files.files;
                    recordingPreview.src = URL.createObjectURL(blob);
                    recordingPreview.hidden = false;
                    recordingStatus.textContent = 'تم حفظ التسجيل وسيتم إرساله مع التهنئة.';
                    startRecording.disabled = false;
                    stopRecording.disabled = true;
                });

                mediaRecorder.start();
                startRecording.disabled = true;
                stopRecording.disabled = false;
                recordingStatus.textContent = 'جاري التسجيل... سيتوقف تلقائيًا عند 10 ثواني.';

                recordingTimer = window.setInterval(function () {
                    recordingSeconds += 1;
                    setRecordingTime(recordingSeconds);

                    if (recordingSeconds >= 10) {
                        finishRecording();
                    }
                }, 1000);
            } catch (error) {
                recordingStatus.textContent = 'لم يتم السماح باستخدام الميكروفون.';
            }
        });

        stopRecording?.addEventListener('click', finishRecording);

        wishForm?.addEventListener('submit', function (event) {
            if (mediaRecorder && mediaRecorder.state === 'recording') {
                event.preventDefault();
                recordingStatus.textContent = 'أوقف التسجيل أولًا قبل إرسال التهنئة.';
                return;
            }

            const button = this.querySelector('button[type="submit"]');
            if (! button) {
                return;
            }

            button.disabled = true;
            button.textContent = button.dataset.loadingText;
        });
    </script>
@endpush
