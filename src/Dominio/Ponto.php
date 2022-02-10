<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Dominio;

use InvalidArgumentException;
use JsonSerializable;
use SolidBase\Matematica\Aritimetica\Numero;

/**
 * @property-read Numero $x
 * @property-read Numero $y
 * @property-read Numero $z
 */
class Ponto implements PrecisaoInterface, JsonSerializable
{
    protected Numero $x;
    protected Numero $y;
    protected Numero $z;

    public function __construct(
        float|Numero $x = 0,
        float|Numero $y = 0,
        float|Numero $z = 0
    ) {
        $this->x = numero($x);
        $this->y = numero($y);
        $this->z = numero($z);
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

    public function __get($name): Numero
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

    public function distanciaParaPonto(self $ponto): Numero
    {
        $x2 = potencia(subtrair($ponto->x, $this->x), 2);
        $y2 = potencia(subtrair($ponto->y, $this->y), 2);
        $z2 = potencia(subtrair($ponto->z, $this->z), 2);

        return raiz(somar($x2, $y2)->somar($z2));
    }

    public function somar(self $ponto): static
    {
        $x = somar($this->x, $ponto->x);
        $y = somar($this->y, $ponto->y);
        $z = somar($this->z, $ponto->z);

        return new static($x, $y, $z);
    }

    public function subtrair(self $ponto): static
    {
        $x = subtrair($this->x, $ponto->x);
        $y = subtrair($this->y, $ponto->y);
        $z = subtrair($this->z, $ponto->z);

        return new static($x, $y, $z);
    }

    public function pontoMedio(self $ponto): static
    {
        $x = dividir(somar($this->x, $ponto->x), 2);
        $y = dividir(somar($this->y, $ponto->y), 2);
        $z = dividir(somar($this->z, $ponto->z), 2);

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
        if ($this->x->valor() >= 0 && $this->y->valor() >= 0) {
            return 1;
        }
        if ($this->x->valor() < 0 && $this->y->valor() >= 0) {
            return 2;
        }
        if ($this->x->valor() < 0 && $this->y->valor() < 0) {
            return 3;
        }

        return 4;
    }
}
