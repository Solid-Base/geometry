<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Dominio;

use InvalidArgumentException;

/**
 * @property-read Ponto $centro
 * @property-read float $raio
 * @property-read float $anguloInicial
 * @property-read float $anguloFinal
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
            default => throw new InvalidArgumentException('A propriedade solicitada não existe')
        };
    }
}
