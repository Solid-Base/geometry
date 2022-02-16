<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Circulo;

use DomainException;
use Solidbase\Geometria\Dominio\Arco;
use Solidbase\Geometria\Dominio\Fabrica\PolilinhaFabrica;
use Solidbase\Geometria\Dominio\Polilinha;
use Solidbase\Geometria\Dominio\PontoPoligono;

class ConverteArcoPoligono
{
    private function __construct()
    {
    }

    public static function executar(Arco $arco, int $numeroDivisao): Polilinha
    {
        if ($numeroDivisao <= 3) {
            throw new DomainException('O número de divisão deve ser maior que 0');
        }
        $anguloInicial = $arco->anguloInicial;
        $anguloFinal = $arco->anguloFinal;
        if ($anguloFinal < $anguloInicial) {
            $anguloFinal += M_PI * 2;
        }
        $delta = ($anguloFinal - $anguloInicial) / ($numeroDivisao - 1);
        $pontos = [];
        $raio = $arco->raio;
        $centro = $arco->centro;
        for ($i = 0; $i < $numeroDivisao; ++$i) {
            $angulo = $anguloInicial + $delta;
            $x = $raio * cos($angulo * $i) + $centro->x;
            $y = $raio * sin($angulo * $i) + $centro->y;
            $z = $centro->z;
            $pontos[] = new PontoPoligono($x, $y, $z);
        }

        return PolilinhaFabrica::criarPolilinhaPontos($pontos);
    }
}
