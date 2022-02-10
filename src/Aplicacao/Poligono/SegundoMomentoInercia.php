<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Poligono;

use Solidbase\Geometria\Dominio\Polilinha;
use SolidBase\Matematica\Aritimetica\Numero;

class SegundoMomentoInercia
{
    private function __construct()
    {
    }

    /**
     * @return ?Numero[]
     */
    public static function executar(Polilinha $poligono): ?array
    {
        if (\count($poligono) < 3) {
            return null;
        }
        $somaX = numero(0, PRECISAO_SOLIDBASE);
        $somaY = numero(0, PRECISAO_SOLIDBASE);
        $pontos = $poligono->pontos();
        $numPontos = \count($poligono);

        for ($i = 0; $i < $numPontos - 1; ++$i) {
            $ponto = $pontos[$i];
            $proximo = $pontos[$i + 1];
            $comum = multiplicar($ponto->x, $proximo->y)->subtrair(multiplicar($proximo->x, $ponto->y));
            $somaY->somar(
                potencia($proximo->x, 2)
                    ->somar(multiplicar($ponto->x, $proximo->x))
                    ->somar(potencia($ponto->x, 2))
                    ->multiplicar($comum)
            );
            $somaX->somar(
                potencia($proximo->y, 2)
                    ->somar(multiplicar($ponto->y, $proximo->y))
                    ->somar(potencia($ponto->y, 2))
                    ->multiplicar($comum)
            );
        }

        $x = dividir($somaX, 12);
        $y = dividir($somaY, 12);

        return [$x, $y];
    }
}
