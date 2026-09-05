<?php

if (! function_exists('irt')) {
    function irt(float|int|string|null $amount): string
    {
        return number_format((float) $amount, 0, '.', ',').' تومان';
    }
}
