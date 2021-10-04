<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Poligono;

use DomainException;
use Solidbase\Geometria\Aplicacao\Poligono\AreaPoligono as PoligonoAreaPoligono;
use Solidbase\Geometria\Dominio\Polilinha;
use Solidbase\Geometria\Dominio\Ponto;

class CentroPoligono
{
    private float $area;

    public function __construct(private Polilinha $poligono)
    {
        $poligono->fecharPolilinha();
        $area = new PoligonoAreaPoligono($poligono);
        $this->area = $area->executar();
    }

    public function executar(): Ponto
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
            $comum = $ponto->x * $proximo->y - $ponto->y * $proximo->x;
            $somaX += ($proximo->x + $ponto->x) * $comum;
            $somaY += ($proximo->y + $ponto->y) * $comum;
        }

        $x = ($somaX / (6 * $this->area));
        $y = ($somaY / (6 * $this->area));

        return new Ponto($x, $y);
    }
}
