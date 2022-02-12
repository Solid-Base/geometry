<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Poligono;

use Solidbase\Geometria\Dominio\Fabrica\PolilinhaFabrica;
use Solidbase\Geometria\Dominio\Polilinha;

class RetanguloEquivalente
{
    public function __construct()
    {
    }

    public function apartirInercia(float|int $inerciaX, float|int $inerciaY): Polilinha
    {
        $hx = ($inerciaX ** 3) * (12 ** 2) / $inerciaY;
        $h = $hx ** (1 / 8);
        $b = $inerciaX * 12 / ($h ** 3);

        return PolilinhaFabrica::criarPoligonoRetangular($b, $h);
    }

    public function apartirArea(float|int $area): Polilinha
    {
        $a = sqrt($area);

        return PolilinhaFabrica::criarPoligonoRetangular($a, $a);
    }
}
