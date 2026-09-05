<?php

namespace App\Enums;

enum OperationMode: string
{
    case LocalOnly = 'local_only';
    case Connected = 'connected';

    public function label(): string
    {
        return match ($this) {
            self::LocalOnly => 'فقط محلی (بدون اینترنت)',
            self::Connected => 'متصل',
        };
    }
}
