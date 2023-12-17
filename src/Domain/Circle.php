<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Domain;

use InvalidArgumentException;
use Solidbase\Geometry\Application\Transform\Transform;
use Solidbase\Geometry\Domain\Trait\TransformTrait;
use Solidbase\Geometry\Domain\Transform as DomainTransform;

/**
 * @property-read Point     $center
 * @property-read float|int $radius
 */
class Circle implements DomainTransform
{
    use TransformTrait;
    private float|int $radius;

    public function __construct(private Point $center, float|int $radius)
    {
        if (sbLessThan($radius, 0)) {
            throw new InvalidArgumentException('O raio do circulo deve ser um numero positivo maior que zero');
        }
        $this->radius = $radius;
    }

    public function __clone()
    {
        $this->center = clone $this->center;
    }

    public function __get($name)
    {
        return match ($name) {
            'center' => $this->center,
            'radius' => $this->radius,
            default => throw new InvalidArgumentException('A propriedade solicitada não existe')
        };
    }

    public function area(): float
    {
        return M_PI * $this->radius ** 2;
    }

    public function perimeter(): float
    {
        return 2 * M_PI * $this->radius;
    }

    public function pointInnerCircle(Point $ponto): bool
    {
        return sbBetween(0, $this->center->distanceToPoint($ponto), $this->radius);
    }

    public function pointBelongsToBorderCircle(Point $ponto): bool
    {
        return sbIsZero($this->center->distanceToPoint($ponto) - $this->radius);
    }

    public function applyTransform(Transform $transformacao): static
    {
        $this->center = $transformacao->applyToPoint($this->center);
        $this->radius *= $transformacao->getScale();

        return $this;
    }
}
