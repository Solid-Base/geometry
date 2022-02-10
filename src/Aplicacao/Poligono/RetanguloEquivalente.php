<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Poligono;

use Solidbase\Geometria\Dominio\Fabrica\PolilinhaFabrica;
use Solidbase\Geometria\Dominio\Polilinha;
use SolidBase\Matematica\Aritimetica\Numero;

class RetanguloEquivalente
{
    public function __construct()
    {
    }

    public function apartirInercia(float|Numero $inerciaX, float|Numero $inerciaY): Polilinha
    {
        $hx = potencia($inerciaX, 3)->multiplicar(potencia(12, 2))->dividir($inerciaY);
        $h = $hx->valor() ** (1 / 8);
        $b = multiplicar($inerciaX, 12)->dividir(potencia($h, 3))->valor();

        return PolilinhaFabrica::criarPoligonoRetangular($b, $h);
    }

    public function apartirArea(float|Numero $area): Polilinha
    {
        $a = raiz($area);

        return PolilinhaFabrica::criarPoligonoRetangular($a->valor(), $a->valor());
    }
}
