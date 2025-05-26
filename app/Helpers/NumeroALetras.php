<?php

namespace App\Helpers;

class NumeroALetras
{
    public static function convertir($numero)
    {
        $formatter = new \NumberFormatter('es', \NumberFormatter::SPELLOUT);
        return $formatter->format($numero);
    }
}
