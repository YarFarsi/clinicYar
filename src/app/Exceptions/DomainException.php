<?php

namespace App\Exceptions;

use Exception;

class DomainException extends Exception
{
    public static function slotTaken(): self
    {
        return new self('این زمان قبلاً رزرو شده است.');
    }

    public static function outsideWorkingHours(): self
    {
        return new self('این زمان در ساعات کاری پزشک نیست.');
    }

    public static function doctorUnavailable(): self
    {
        return new self('پزشک در این بازه در دسترس نیست.');
    }

    public static function forbiddenRecord(): self
    {
        return new self('دسترسی به این پرونده را ندارید.');
    }

    public static function installerLocked(): self
    {
        return new self('نصب قبلاً انجام شده است.');
    }
}
