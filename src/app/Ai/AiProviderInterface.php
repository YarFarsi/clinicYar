<?php

namespace App\Ai;

interface AiProviderInterface
{
    public function summarizeNonClinical(string $text): string;

    public function suggestFollowUpMessage(string $context): string;
}
