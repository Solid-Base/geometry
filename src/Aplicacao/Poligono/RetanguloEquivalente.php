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

    public function apartirInercia(float $inerciaX, float $inerciaY): Polilinha
    {
        $h = (($inerciaX ** 3) * (12 ** 2) / $inerciaY) ** (1 / 8);
        $b = $inerciaX * 12 / ($h ** 3);

        return PolilinhaFabrica::criarPoligonoRetangular($b, $h);
    }

    public function apartirArea(float $area): Polilinha
    {
        $a = sqrt($area);

        return PolilinhaFabrica::criarPoligonoRetangular($a, $a);
    }
}
