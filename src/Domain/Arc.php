<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Domain;

use InvalidArgumentException;
use Solidbase\Geometry\Application\Transform\Transform;
use Solidbase\Geometry\Domain\Factory\CircleArchFactory;
use Solidbase\Geometry\Domain\Factory\VectorFactory;
use Solidbase\Geometry\Domain\Trait\TransformTrait;
use Solidbase\Geometry\Domain\Transform as DomainTransform;

/**
 * @property-read float|int $angleTotal
 * @property-read float|int $area
 * @property-read float|int $length
 */
class Arc implements DomainTransform
{
    use TransformTrait;


    public function __construct(public Point $center, public float|int $radius, public float|int $startAngle, public float|int $endAngle)
    {
        if (sbLessThan($radius, 0)) {
            throw new InvalidArgumentException('O raio do arco deve ser um numero positivo maior que zero');
        }
    }

    public function __get($name)
    {
        return match ($name) {
            'center' => $this->center,
            'radius' => $this->radius,
            'startAngle' => $this->startAngle,
            'endAngle' => $this->endAngle,
            'angleTotal' => $this->getAngleTotal(),
            'length' => $this->length(),
            'area' => $this->area(),
            default => throw new InvalidArgumentException('A propriedade solicitada não existe')
        };
    }

    public function __clone()
    {
        $this->center = clone $this->center;
    }

    public function applyTransform(Transform $transformacao): static
    {
        $startPoint = $transformacao->applyToPoint($this->getStartPoint());
        $endPoint = $transformacao->applyToPoint($this->getEndPoint());
        $center = $transformacao->applyToPoint($this->center);
        $arc = CircleArchFactory::CreateArcFromCenterStartEnd($center, $startPoint, $endPoint);
        $this->center = $arc->center;
        $this->startAngle = $arc->startAngle;
        $this->endAngle = $arc->endAngle;
        $this->radius = sbNormalize($arc->radius);
        unset($arc);

        return $this;
    }

    public function getAngleTotal(): float
    {
        $total = $this->endAngle - $this->startAngle;
        if (sbLessThan($total, 0)) {
            $total += M_PI * 2;
        }

        return $total;
    }

    public function length(): float
    {
        return $this->getAngleTotal() * $this->radius;
    }

    public function area(): float
    {
        $anguloTotal = $this->getAngleTotal();

        return $this->radius ** 2 * ($anguloTotal - sin($anguloTotal)) / 2;
    }

    public function getStartPoint(): Point
    {
        $transformaca = Transform::CreateRotationAroundPoint(VectorFactory::CreateBaseZ(), $this->startAngle, $this->center);
        $ponto = $this->center->add(VectorFactory::CreateBaseX()->scalar($this->radius));

        return $transformaca->applyToPoint($ponto);
    }

    public function getEndPoint(): Point
    {
        $transformaca = Transform::CreateRotationAroundPoint(VectorFactory::CreateBaseZ(), $this->endAngle, $this->center);
        $ponto = $this->center->add(VectorFactory::CreateBaseX()->scalar($this->radius));

        return $transformaca->applyToPoint($ponto);
    }

    public function pointBelongsToArc(Point $ponto): bool
    {
        if (!sbIsZero($this->center->distanceToPoint($ponto) - $this->radius)) {
            return false;
        }
        $pInicial = $this->getStartPoint();
        $pFinal = $this->getEndPoint();
        if (sbIsZero($pFinal->distanceToPoint($ponto)) || sbIsZero($pInicial->distanceToPoint($ponto))) {
            return true;
        }
        $arco = CircleArchFactory::CreateArcFromThreePoint($pInicial, $ponto, $pFinal);

        return sbIsZero(($arco->startAngle - $this->startAngle)) && sbIsZero(($arco->endAngle - $this->endAngle));
    }
}
