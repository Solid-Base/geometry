<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Dominio;

/**
 * @property-read float $concordancia
 */
class PontoPoligono extends Ponto
{
    private float $concordancia = 0;

    public function __get($name): float
    {
        return match ($name) {
            'concordancia' => $this->concordancia,
            default => parent::__get($name)
        };
    }

    /**
     * Informar o valor concordancia.
     */
    public function informarConcordancia(float $concordancia): self
    {
        $this->concordancia = $concordancia;

        return $this;
    }
}
