<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Domain;

/**
 * @property-read float $agreement
 */
class PointOfPolygon extends Point
{
    private float $agreement;

    public function __construct(
        float|int $x = 0,
        float|int $y = 0,
        float|int $z = 0
    ) {
        parent::__construct($x, $y, $z);
        $this->agreement = 0;
    }

    public function __get($name): float
    {
        return match ($name) {
            'concordancia' => $this->agreement,
            default => parent::__get($name)
        };
    }

    public function __serialize(): array
    {
        $retorno = parent::__serialize();
        $retorno['agreement'] = $this->agreement;

        return $retorno;
    }

    public function __unserialize(array $data): void
    {
        parent::__unserialize($data);
        $this->agreement = $data['concordancia'];
    }

    /**
     * Informar o valor concordancia.
     */
    public function setAgreement(float|int $concordancia): self
    {
        $this->agreement = $concordancia;

        return $this;
    }
}
