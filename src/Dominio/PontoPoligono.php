<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Dominio;

use SolidBase\Matematica\Aritimetica\Numero;

/**
 * @property-read Numero $concordancia
 */
class PontoPoligono extends Ponto
{
    private Numero $concordancia;

    public function __construct(
        float|Numero $x = 0,
        float|Numero $y = 0,
        float|Numero $z = 0
    ) {
        parent::__construct($x, $y, $z);
        $this->concordancia = numero(0, PRECISAO_SOLIDBASE);
    }

    public function __get($name): Numero
    {
        return match ($name) {
            'concordancia' => $this->concordancia,
            default => parent::__get($name)
        };
    }

    public function __serialize(): array
    {
        $retorno = parent::__serialize();
        $retorno['concordancia'] = $this->concordancia;

        return $retorno;
    }

    public function __unserialize(array $data): void
    {
        parent::__unserialize($data);
        $this->concordancia = $data['concordancia'];
    }

    /**
     * Informar o valor concordancia.
     */
    public function informarConcordancia(float|Numero $concordancia): self
    {
        $this->concordancia = numero($concordancia);

        return $this;
    }
}
