<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Dominio;

/**
 * @property-read float $concordancia
 */
class PontoPoligono extends Ponto
{
    private float $concordancia;

    public function __construct(
        float|int $x = 0,
        float|int $y = 0,
        float|int $z = 0
    ) {
        parent::__construct($x, $y, $z);
        $this->concordancia = 0;
    }

    public function __get($name): float
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
    public function informarConcordancia(float|int $concordancia): self
    {
        $this->concordancia = $concordancia;

        return $this;
    }
}
