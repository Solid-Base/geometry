<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Domain;

use DomainException;
use InvalidArgumentException;
use Solidbase\Geometry\Application\Transform\Transform;
use Solidbase\Geometry\Domain\Factory\VectorFactory;
use Solidbase\Geometry\Domain\Trait\TransformTrait;
use Solidbase\Geometry\Domain\Transform as DomainTransform;

/**
 * @property-read Point $center
 * @property-read float $largestRadius
 * @property-read float $minorRadius
 * @property-read Vector $direction
 */
class Elipse implements DomainTransform
{
    use TransformTrait;
    private Vector $direction;

    public function __construct(private Point $center, private float $largestRadius, private float $minorRadius, float $angle = 0)
    {
        if ($largestRadius <= 0 || $this->minorRadius <= 0) {
            throw new InvalidArgumentException('Os raios da elipse deve ser um numero positivo maior que zero');
        }
        if ($minorRadius >= $largestRadius) {
            throw new DomainException('O raio menor nõa deve ser maior que o raio maior');
        }
        $this->direction = VectorFactory::CreateFromDirectionAndModule($angle);
    }

    public function __clone()
    {
        $this->center = clone $this->center;
        $this->direction = clone $this->direction;
    }

    public function __get($name)
    {
        return match ($name) {
            'center' => $this->center,
            'largestRadius' => $this->largestRadius,
            'minorRadius' => $this->minorRadius,
            'direction' => $this->direction,
            default => throw new InvalidArgumentException('A propriedade solicitada não existe')
        };
    }

    public function area(): float
    {
        return M_PI * $this->largestRadius * $this->minorRadius;
    }

    public function perimeter(): float
    {
        $c = sqrt($this->largestRadius ** 2 + $this->minorRadius ** 2);
        $e = $c / $this->largestRadius;

        return $this->largestRadius * M_PI * (2 - ($e ** 2) / 2 + (3 * $e ** 4) / 16);
    }

    public function applyTransform(Transform $transformacao): static
    {
        $this->center = $transformacao->applyToPoint($this->center);
        $this->direction = $transformacao->applyToVector($this->direction);

        $this->largestRadius *= $transformacao->getScale();
        $this->minorRadius *= $transformacao->getScale();

        return $this;
    }
}
