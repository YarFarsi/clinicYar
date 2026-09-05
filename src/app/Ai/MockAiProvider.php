<?php

namespace App\Ai;

class MockAiProvider implements AiProviderInterface
{
    public function summarizeNonClinical(string $text): string
    {
        $text = trim($text);
        if ($text === '') {
            return 'خلاصه‌ای برای نمایش موجود نیست.';
        }

        return 'خلاصه مدیریتی (غیرتشخیصی): '.mb_substr($text, 0, 120);
    }

    public function suggestFollowUpMessage(string $context): string
    {
        return 'یادآوری مراجعه بعدی بیمار. این متن پیشنهاد مدیریتی است و تشخیص پزشکی نیست.';
    }
}
