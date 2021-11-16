<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Dominio;

use InvalidArgumentException;

/**
 * @property-read Ponto $centro
 * @property-read float $raio
 * @property-read float $anguloInicial
 * @property-read float $anguloFinal
 * @property-read float $anguloTotal
 * @property-read float $area
 * @property-read float $comprimento
 */
class Arco
{
    public function __construct(private Ponto $centro, private float $raio, private float $anguloInicial, private float $anguloFinal)
    {
        if ($raio <= 0) {
            throw new InvalidArgumentException('O raio do arco deve ser um numero positivo maior que zero');
        }
    }

    public function __get($name)
    {
        return match ($name) {
            'centro' => $this->centro,
            'raio' => $this->raio,
            'anguloInicial' => $this->anguloInicial,
            'anguloFinal' => $this->anguloFinal,
            'anguloTotal' => $this->anguloTotal(),
            'comprimento' => $this->comprimentoArco(),
            'area' => $this->area(),
            default => throw new InvalidArgumentException('A propriedade solicitada não existe')
        };
    }

    public function anguloTotal(): float
    {
        $total = $this->anguloFinal - $this->anguloInicial;
        if ($total < 0) {
            $total += 2 * M_PI;
        }

        return $total;
    }

    public function comprimentoArco(): float
    {
        return $this->anguloTotal() * $this->raio;
    }

    public function area(): float
    {
        $anguloTotal = $this->anguloTotal();

        return ($this->raio ** 2) * ($anguloTotal - sin($anguloTotal)) / 2;
    }
}
