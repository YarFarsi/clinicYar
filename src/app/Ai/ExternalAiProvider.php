<?php

namespace App\Ai;

class ExternalAiProvider implements AiProviderInterface
{
    public function summarizeNonClinical(string $text): string
    {
        return app(MockAiProvider::class)->summarizeNonClinical($text);
    }

    public function suggestFollowUpMessage(string $context): string
    {
        return app(MockAiProvider::class)->suggestFollowUpMessage($context);
    }
}
