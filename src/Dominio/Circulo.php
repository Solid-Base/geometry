<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Dominio;

use InvalidArgumentException;
use SolidBase\Matematica\Aritimetica\Numero;

/**
 * @property-read Ponto  $centro
 * @property-read Numero $raio
 */
class Circulo
{
    private Numero $raio;

    public function __construct(private Ponto $centro, float|Numero $raio)
    {
        if (eMenor($raio, 0)) {
            throw new InvalidArgumentException('O raio do circulo deve ser um numero positivo maior que zero');
        }
        $this->raio = numero($raio, PRECISAO_SOLIDBASE);
    }

    public function __get($name)
    {
        return match ($name) {
            'centro' => $this->centro,
            'raio' => $this->raio,
            default => throw new InvalidArgumentException('A propriedade solicitada não existe')
        };
    }

    public function area(): Numero
    {
        return multiplicar(S_PI, potencia($this->raio, 2));
    }

    public function perimetro(): Numero
    {
        return multiplicar(multiplicar(S_PI, 2), $this->raio);
    }

    public function pontoInternoCirculo(Ponto $ponto): bool
    {
        return entre(0, $this->centro->distanciaParaPonto($ponto), $this->raio);
    }

    public function pontoFronteiraCirculo(Ponto $ponto): bool
    {
        return eZero(subtrair($this->centro->distanciaParaPonto($ponto), $this->raio));
    }
}
