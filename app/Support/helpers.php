<?php

if (! function_exists('money')) {
    /** Montant en francs CFA, avec espaces insécables : « 12 500 FCFA ». */
    function money(int|float|null $amount): string
    {
        return number_format((int) $amount, 0, ',', "\u{202F}")."\u{00A0}FCFA";
    }
}
