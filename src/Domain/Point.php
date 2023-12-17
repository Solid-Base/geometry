<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Domain;

use InvalidArgumentException;
use JsonSerializable;
use Solidbase\Geometry\Application\Transform\Transform;
use Solidbase\Geometry\Domain\Enum\QuadrantEnum;
use Solidbase\Geometry\Domain\Trait\TransformTrait;
use Solidbase\Geometry\Domain\Transform as DomainTransform;

/**
 * @property-read float $x
 * @property-read float $y
 * @property-read float $z
 */
class Point implements JsonSerializable, DomainTransform
{
    use TransformTrait;
    protected float $_x;
    protected float $_y;
    protected float $_z;

    public function __construct(
        float $x = 0,
        float $y = 0,
        float $z = 0
    ) {
        $this->_x = sbNormalize($x);
        $this->_y = sbNormalize($y);
        $this->_z = sbNormalize($z);
    }

    public function __serialize(): array
    {
        return [
            'x' => $this->_x,
            'y' => $this->_y,
            'z' => $this->_z,
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->_x = $data['x'];
        $this->_y = $data['y'];
        $this->_z = $data['z'];
    }

    public function __get($name): float
    {
        return match ($name) {
            'x' => $this->_x,
            'y' => $this->_y,
            'z' => $this->_z,
            default => throw new InvalidArgumentException('Prorpriedade solicitada não existe')
        };
    }

    public function applyTransform(Transform $transformacao): static
    {
        $novo = $transformacao->applyToPoint($this);
        $this->_x = $novo->_x;
        $this->_y = $novo->_y;
        $this->_z = $novo->_z;

        return $this;
    }

    public function jsonSerialize(): mixed
    {
        return $this->__serialize();
    }

    public function distanceToPoint(self $ponto): float
    {
        $x2 = ($ponto->_x - $this->_x) ** 2;
        $y2 = ($ponto->_y - $this->_y) ** 2;
        $z2 = ($ponto->_z - $this->_z) ** 2;

        return sqrt($x2 + $y2 + $z2);
    }

    public function add(self $ponto): static
    {
        $x = ($this->_x + $ponto->_x);
        $y = ($this->_y + $ponto->_y);
        $z = ($this->_z + $ponto->_z);

        return new static($x, $y, $z);
    }

    public function difference(self $ponto): static
    {
        $x = ($this->_x - $ponto->_x);
        $y = ($this->_y - $ponto->_y);
        $z = ($this->_z - $ponto->_z);

        return new static($x, $y, $z);
    }

    public function midpoint(self $ponto): static
    {
        $x = ($this->_x + $ponto->_x) / 2;
        $y = ($this->_y + $ponto->_y) / 2;
        $z = ($this->_z + $ponto->_z) / 2;

        return new static($x, $y, $z);
    }

    public function isEquals(self $ponto): bool
    {
        $distancia = $this->distanceToPoint($ponto);

        return sbIsZero($distancia);
    }

    public function toArray(): array
    {
        return $this->__serialize();
    }

    public function getQuadrant(): QuadrantEnum
    {
        $angulo = sbNormalizeAngle(atan2($this->_y, $this->_x));
        if (sbIsZero($angulo) || sbBetween(0, $angulo, M_PI / 2, false)) {
            return QuadrantEnum::First;
        }
        if (sbBetween(M_PI / 2, $angulo, M_PI, false) || sbEquals($angulo, M_PI / 2)) {
            return QuadrantEnum::Second;
        }
        if (sbBetween(M_PI, $angulo, 3 * M_PI / 2, false) || sbEquals($angulo, M_PI)) {
            return QuadrantEnum::Third;
        }

        return QuadrantEnum::Fourth;
    }
}
