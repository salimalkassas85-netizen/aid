<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWishRequest extends FormRequest
{
    public const RELATIONSHIPS = [
        'mother' => 'الأم',
        'father' => 'الأب',
        'wife' => 'الزوجة',
        'husband' => 'الزوج',
        'fiancee' => 'الخطيبة',
        'friend' => 'صديق',
        'customer' => 'عميل',
        'manager' => 'مدير',
        'team' => 'فريق العمل',
        'general' => 'عام',
    ];

    public const STYLES = [
        'elegant' => 'راقية',
        'religious' => 'دينية',
        'funny' => 'مضحكة',
        'romantic' => 'رومانسية',
        'corporate' => 'رسمية / شركات',
    ];

    public const AUDIO_STYLES = [
        'takbeer' => 'تكبيرات العيد',
        'nasheed' => 'أنشودة هادئة',
        'soft' => 'موسيقى هادئة مرخصة',
        'none' => 'بدون صوت',
    ];

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'sender_name' => trim((string) $this->input('sender_name')),
            'receiver_name' => trim((string) $this->input('receiver_name')),
        ]);
    }

    public function rules(): array
    {
        return [
            'sender_name' => ['required', 'string', 'max:50', 'not_regex:/[<>]/'],
            'receiver_name' => ['required', 'string', 'max:50', 'not_regex:/[<>]/'],
            'relationship' => ['required', Rule::in(array_keys(self::RELATIONSHIPS))],
            'style' => ['required', Rule::in(array_keys(self::STYLES))],
            'audio_style' => ['nullable', Rule::in(array_keys(self::AUDIO_STYLES))],
        ];
    }

    public function messages(): array
    {
        return [
            'sender_name.required' => 'اكتب اسم المرسل.',
            'sender_name.max' => 'اسم المرسل يجب ألا يتجاوز 50 حرفًا.',
            'sender_name.not_regex' => 'اسم المرسل لا يمكن أن يحتوي على رموز HTML.',
            'receiver_name.required' => 'اكتب اسم المستلم.',
            'receiver_name.max' => 'اسم المستلم يجب ألا يتجاوز 50 حرفًا.',
            'receiver_name.not_regex' => 'اسم المستلم لا يمكن أن يحتوي على رموز HTML.',
            'relationship.required' => 'اختر صلة القرابة.',
            'relationship.in' => 'صلة القرابة غير صحيحة.',
            'style.required' => 'اختر أسلوب التهنئة.',
            'style.in' => 'أسلوب التهنئة غير صحيح.',
            'audio_style.in' => 'اختيار الصوت غير صحيح.',
        ];
    }
}
