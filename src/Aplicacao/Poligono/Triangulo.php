<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Poligono;

use Solidbase\Geometria\Dominio\Ponto;

class Triangulo
{
    public function __construct(private Ponto $p1, private Ponto $p2, private Ponto $p3)
    {
    }

    public function centro(): Ponto
    {
        $x = $this->p1->x + $this->p2->x + $this->p3->x;
        $y = $this->p1->y + $this->p2->y + $this->p3->y;

        return new Ponto($x, $y);
    }

    public function area(): float
    {
        $area = (
            $this->p1->x * $this->p2->y -
            $this->p1->y * $this->p2->x -
            $this->p1->y * $this->p3->x -
            $this->p1->x * $this->p3->y +
            $this->p2->x * $this->p3->y -
            $this->p3->x * $this->p2->y
        ) / 2;

        return abs($area);
    }
}
