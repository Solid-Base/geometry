<?php

declare(strict_types=1);

if (!function_exists('areaCirculoRaio')) {
    function areaCirculoRaio(float $raio): float
    {
        return M_PI * ($raio ** 2);
    }
}

if (!function_exists('areaCirculoDiametro')) {
    function areaCirculoDiametro(float $diametro): float
    {
        return (M_PI * ($diametro ** 2)) / 4;
    }
}
