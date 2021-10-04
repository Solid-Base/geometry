<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Dominio;

use InvalidArgumentException;

/**
 * @property-read float $x
 * @property-read float $y
 * @property-read float $z
 */
class Ponto implements PrecisaoInterface
{
    public function __construct(
        protected float $x = 0,
        protected float $y = 0,
        protected float $z = 0
    ) {
    }

    public function __get($name): float
    {
        return match ($name) {
            'x' => $this->x,
            'y' => $this->y,
            'z' => $this->z,
            default => throw new InvalidArgumentException('Prorpriedade solicitada não existe')
        };
    }

    public function distanciaParaPonto(self $ponto): float
    {
        return sqrt(($ponto->x - $this->x) ** 2 + ($ponto->y - $this->y) ** 2 + ($ponto->z - $this->z) ** 2);
    }

    public function somar(self $ponto): self
    {
        $x = $this->x + $ponto->x;
        $y = $this->y + $ponto->y;
        $z = $this->z + $ponto->z;


        return new self($x, $y, $z);
    }

    public function subtrair(self $ponto): self
    {
        $x = $ponto->x - $this->x;
        $y = $ponto->y - $this->y;
        $z = $ponto->z - $this->z;

        return new self($x, $y, $z);
    }

    public function pontoMedio(self $ponto): self
    {
        $x = ($this->x + $ponto->x) / 2;
        $y = ($this->y + $ponto->y) / 2;
        $z = ($this->z + $ponto->z) / 2;

        return new self($x, $y, $z);
    }

    public function eIgual(self $ponto): bool
    {
        $distancia = $this->distanciaParaPonto($ponto);

        return $distancia <= $this::PRECISAO;
    }

    protected function quadrante(): int
    {
        if ($this->x >= 0 && $this->y >= 0) {
            return 1;
        }
        if ($this->x < 0 && $this->y >= 0) {
            return 2;
        }
        if ($this->x < 0 && $this->y < 0) {
            return 3;
        }

        return 4;
    }
}
