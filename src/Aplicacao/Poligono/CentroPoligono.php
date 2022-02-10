<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Poligono;

use Solidbase\Geometria\Dominio\Polilinha;
use Solidbase\Geometria\Dominio\Ponto;

class CentroPoligono
{
    private function __construct()
    {
    }

    public static function executar(Polilinha $poligono): ?Ponto
    {
        $area = AreaPoligono::executar($poligono);
        if (null === $area) {
            return null;
        }
        $somaX = numero(0, PRECISAO_SOLIDBASE);
        $somaY = numero(0, PRECISAO_SOLIDBASE);
        $pontos = $poligono->pontos();
        $numPontos = \count($poligono);

        for ($i = 0; $i < $numPontos - 1; ++$i) {
            $ponto = $pontos[$i];
            $proximo = $pontos[$i + 1];
            $comum = subtrair(multiplicar($ponto->x, $proximo->y), multiplicar($ponto->y, $proximo->x));
            $somaX->somar(somar($proximo->x, $ponto->x)->multiplicar($comum));
            $somaY->somar(somar($proximo->y, $ponto->y)->multiplicar($comum));
        }

        $x = dividir($somaX, multiplicar($area, 6));
        $y = dividir($somaY, multiplicar($area, 6));

        return new Ponto($x, $y);
    }
}
