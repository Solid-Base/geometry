<?php

declare(strict_types=1);

if (!function_exists('circleAreaRadius')) {
    function circleAreaRadius(float $raio): float
    {
        return M_PI * ($raio ** 2);
    }
}

if (!function_exists('circleAreaDiameter')) {
    function circleAreaDiameter(float $diametro): float
    {
        return (M_PI * ($diametro ** 2)) / 4;
    }
}
