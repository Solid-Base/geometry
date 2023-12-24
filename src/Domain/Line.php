<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Domain;

use InvalidArgumentException;
use JsonSerializable;
use Solidbase\Geometry\Application\Transform\Transform as TransformTransform;
use Solidbase\Geometry\Domain\Trait\TransformTrait;
use Solidbase\Geometry\Domain\Transform ;

/**
 * @property-read Point     $origin
 * @property-read Point     $end
 * @property-read Vector     $direction
 * @property-read float|int $length
 */
class Line implements JsonSerializable, Transform
{
    use TransformTrait;
    private float|int $_length;

    public function __construct(
        private Point $_origin,
        private Vector $direction,
        float|int $length
    ) {
        $this->direction = $direction->getUnitary();
        $this->_length = sbNormalize($length);
    }

    public function __get($name)
    {
        return match ($name) {
            'origin' => $this->_origin,
            'end' => $this->getEndPoint(),
            'direction' => $this->direction,
            'length' => $this->_length,
            default => throw new InvalidArgumentException("Prorpriedade {$name} solicitada não existe")
        };
    }

    public function __clone()
    {
        $this->_origin = clone $this->_origin;
        $this->direction = clone $this->direction;
    }

    public function applyTransform(TransformTransform $transformacao): static
    {
        $origem = $transformacao->applyToPoint($this->_origin);
        $direcao = $transformacao->applyToVector($this->direction);
        $this->_origin = $origem;
        $this->direction = $direcao;
        $this->_length *= $transformacao->getScale();

        return $this;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'origem' => $this->_origin,
            'direcao' => $this->direction,
            'comprimento' => $this->_length,
        ];
    }

    public function isParallel(self $linha): bool
    {
        return $linha->direction->hasSameDirection($this->direction);
    }

    public function isCoplanar(self $line): bool
    {
        $origin = $this->_origin->isEquals($line->origin) ? $line->origin : $line->end;
        $vectorFromPoints = Vector::CreateFromPoints($this->_origin, $origin);
        $tripleProduct = $this->direction->tripleProduct($line->direction, $vectorFromPoints);

        return sbIsZero($tripleProduct);
    }

    public function distanceFromPoint(Point $ponto): float
    {
        $vetorAuxiliar = Vector::CreateFromPoints($this->_origin, $ponto);
        $vetorial = $vetorAuxiliar->crossProduct($this->direction);

        return $vetorial->module();
    }

    public function belongsToLine(Point $ponto): bool
    {
        if ($ponto->isEquals($this->origin) || $ponto->isEquals($this->end)) {
            return true;
        }
        $vetor = Vector::CreateFromPoints($ponto, $this->origin)->getUnitary();
        if (!$vetor->hasSameDirection($this->direction)) {
            return false;
        }

        return true;
    }

    public function belongsToSegment(Point $ponto): bool
    {
        if (!$this->belongsToLine($ponto)) {
            return false;
        }
        $distOrigem = $ponto->distanceToPoint($this->origin);
        $distFinal = $ponto->distanceToPoint($this->end);

        return sbLessThan($distOrigem, $this->_length) && sbLessThan($distFinal, $this->_length);
    }

    public function pointAtLength(float|int $comprimento): Point
    {
        $origem = $this->origin;
        $diretor = $this->direction;

        return $origem->add($diretor->scalar($comprimento));
    }

    protected function getEndPoint(): Point
    {
        return $this->pointAtLength($this->_length);
    }

    public function getEquation(): array
    {
        $angle = $this->direction->getAbsoluteAngle();
        $coeficienteAngular = tan($angle);
        $point = $this->origin;
        if($point->x == 0 && $point->y === 0) {
            $point = $this->pointAtLength(1);
        }

        $coeficienteLinear = $point->y - ($coeficienteAngular * $point->x);

        return [$coeficienteAngular,$coeficienteLinear];

    }

    public static function CreateLineFromPoints(Point $point1, Point $point2): Line
    {
        $vetor = Vector::CreateFromPoints($point1, $point2);
        if ($vetor->isZero()) {
            $vetor = Vector::CreateBaseX();
        }
        $comprimento = $point2->distanceToPoint($point1);

        return new Line($point1, $vetor->getUnitary(), $comprimento);
    }

    public static function createLineFromOriginAndDirection(Point $origem, Vector $direcao): Line
    {
        return new Line($origem, $direcao, 1);
    }

    public static function CreateLineFromEquation(?float $slope, float $yIntercept): Line
    {
        if (null === $slope) {
            return new Line(new Point($yIntercept, 0), Vector::CreateBaseY(), 1);
        }
        if (sbIsZero($slope)) {
            return new Line(new Point(0, $yIntercept), Vector::CreateBaseX(), 1);
        }
        $p1 = new Point(0, $yIntercept);
        $p2 = new Point(1, ($slope + $yIntercept));

        return self::CreateLineFromPoints($p1, $p2);
    }
}
