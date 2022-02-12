<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Dominio;

use InvalidArgumentException;

/**
 * @property-read Ponto     $centro
 * @property-read float|int $raio
 */
class Circulo
{
    private float|int $raio;

    public function __construct(private Ponto $centro, float|int $raio)
    {
        if (eMenor($raio, 0)) {
            throw new InvalidArgumentException('O raio do circulo deve ser um numero positivo maior que zero');
        }
        $this->raio = $raio;
    }

    public function __get($name)
    {
        return match ($name) {
            'centro' => $this->centro,
            'raio' => $this->raio,
            default => throw new InvalidArgumentException('A propriedade solicitada não existe')
        };
    }

    public function area(): float
    {
        return M_PI * $this->raio ** 2;
    }

    public function perimetro(): float
    {
        return 2 * M_PI * $this->raio;
    }

    public function pontoInternoCirculo(Ponto $ponto): bool
    {
        return entre(0, $this->centro->distanciaParaPonto($ponto), $this->raio);
    }

    public function pontoFronteiraCirculo(Ponto $ponto): bool
    {
        return eZero($this->centro->distanciaParaPonto($ponto) - $this->raio);
    }
}
