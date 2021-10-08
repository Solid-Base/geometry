<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Poligono;

use DomainException;
use Solidbase\Geometria\Dominio\Polilinha;

class SegundoMomentoInercia
{
    public function __construct(private Polilinha $poligono)
    {
        $poligono->fecharPolilinha();
    }

    public function executar(): array
    {
        if (\count($this->poligono) < 3) {
            throw new DomainException('É necessário ter pelo menos três pontos');
        }
        $somaX = 0;
        $somaY = 0;
        $pontos = $this->poligono->pontos();
        $numPontos = \count($this->poligono);

        for ($i = 0; $i < $numPontos - 1; ++$i) {
            $ponto = $pontos[$i];
            $proximo = $pontos[$i + 1];
            $comum = $ponto->x * $proximo->y - $proximo->x * $ponto->y;
            $somaY += ($proximo->x ** 2 + $ponto->x * $proximo->x + $ponto->x ** 2) * $comum;
            $somaX += ($proximo->y ** 2 + $ponto->y * $proximo->y + $ponto->y ** 2) * $comum;
        }

        $x = $somaX / 12;
        $y = $somaY / 12;

        return [$x, $y];
    }
}
