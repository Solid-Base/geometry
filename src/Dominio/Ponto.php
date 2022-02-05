<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Dominio;

use InvalidArgumentException;
use JsonSerializable;

/**
 * @property-read float $x
 * @property-read float $y
 * @property-read float $z
 */
class Ponto implements PrecisaoInterface, JsonSerializable
{
    public function __construct(
        protected float $x = 0,
        protected float $y = 0,
        protected float $z = 0
    ) {
    }

    public function __serialize(): array
    {
        return [
            'x' => $this->x,
            'y' => $this->y,
            'z' => $this->z,
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->x = $data['x'];
        $this->y = $data['y'];
        $this->z = $data['z'];
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

    public function jsonSerialize(): mixed
    {
        return $this->__serialize();
    }

    public function distanciaParaPonto(self $ponto): float
    {
        return sqrt(($ponto->x - $this->x) ** 2 + ($ponto->y - $this->y) ** 2 + ($ponto->z - $this->z) ** 2);
    }

    public function somar(self $ponto): static
    {
        $x = $this->x + $ponto->x;
        $y = $this->y + $ponto->y;
        $z = $this->z + $ponto->z;

        return new static($x, $y, $z);
    }

    public function subtrair(self $ponto): static
    {
        $x = $this->x - $ponto->x;
        $y = $this->y - $ponto->y;
        $z = $this->z - $ponto->z;

        return new static($x, $y, $z);
    }

    public function pontoMedio(self $ponto): static
    {
        $x = ($this->x + $ponto->x) / 2;
        $y = ($this->y + $ponto->y) / 2;
        $z = ($this->z + $ponto->z) / 2;

        return new static($x, $y, $z);
    }

    public function eIgual(self $ponto): bool
    {
        $distancia = $this->distanciaParaPonto($ponto);

        return eZero($distancia);
    }

    public function toArray(): array
    {
        return $this->__serialize();
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
