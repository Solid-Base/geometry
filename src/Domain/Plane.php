<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Domain;

use DomainException;
use InvalidArgumentException;
use Solidbase\Geometry\Domain\Factory\VectorFactory;

/**
 * @property-read Vector $u
 * @property-read Vector $v
 * @property-read Vector $normal
 * @property-read Point $origin
 */
class Plane
{
    private Vector $u;
    private Vector $v;

    public function __construct(
        private Point $origin,
        private Vector $normal
    ) {
        if ($normal->isZero()) {
            throw new DomainException('Não é possível criar Planos com normal nula');
        }
        $this->normal = $normal->getUnitary();
        $this->u = VectorFactory::CreatePerpendicular($normal)->getUnitary();
        $this->v = $this->normal->crossProduct($this->u)->getUnitary();
    }

    public function __clone()
    {
        $this->u = clone $this->u;
        $this->v = clone $this->v;
        $this->origin = clone $this->origin;
        $this->normal = clone $this->normal;
    }

    public function __get($name)
    {
        return match ($name) {
            'u' => $this->u,
            'v' => $this->v,
            'normal' => $this->normal,
            'origin' => $this->origin,
            default => throw new InvalidArgumentException('Propriedade informada não existe!')
        };
    }

    public function getDistanceToPoint(Point $point): float
    {
        $v = VectorFactory::CreateFromPoints($this->origin, $point);

        return $this->normal->product($v);
    }

    public function isPointOnPlane(Point $ponto): bool
    {
        return sbIsZero($this->getDistanceToPoint($ponto));
    }

    public function getPlaneProjectionPoint(Point $point): Point
    {
        $distancia = $this->getDistanceToPoint($point);

        return $point->difference($this->normal->scalar($distancia));
    }

    public function getPlaneZProjectionPoint(Point $point): Point
    {
        if (sbIsZero($this->normal->z)) {
            throw new DomainException('Não é possível calcular a projeção do ponto no plano Z');
        }
        [$a,$b,$c,$d] = $this->getPlaneEquation();
        $z = (-$d - $b * $point->y - $a * $point->x) / $c;

        return new Point($point->x, $point->y, $z);
    }

    public function getPlaneXProjectionPoint(Point $point): Point
    {
        if (sbIsZero($this->normal->x)) {
            throw new DomainException('Não é possível calcular a projeção do ponto no plano X');
        }
        [$a,$b,$c,$d] = $this->getPlaneEquation();
        $x = (-$d - $b * $point->y - $c * $point->z) / $a;

        return new Point($x, $point->y, $point->z);
        // $p = VetorFabrica::apartirPonto($this->origem);
        // $normal = $this->normal;
        // $comprimento = $normal->produtoInterno($p);
        // $x = ($comprimento - ($normal->y * $ponto->y + $normal->z * $ponto->z)) / $normal->x;
        // $pontoRetorno = clone $ponto;
        // $pontoRetorno->x = $x;

        // return $pontoRetorno;
    }

    public function getPlaneYProjectionPoint(Point $point): Point
    {
        if (sbIsZero($this->normal->y)) {
            throw new DomainException('Não é possível calcular a projeção do ponto no plano Y');
        }
        [$a,$b,$c,$d] = $this->getPlaneEquation();
        $y = (-$d - $a * $point->x - $c * $point->z) / $b;

        return new Point($point->x, $y, $point->z);
        // $p = VetorFabrica::apartirPonto($this->origem);
        // $normal = $this->normal;
        // $comprimento = $normal->produtoInterno($p);
        // $y = ($comprimento - ($normal->x * $ponto->x + $normal->z * $ponto->z)) / $normal->y;
        // $pontoRetorno = clone $ponto;
        // $pontoRetorno->y = $y;

        // return $pontoRetorno;
    }

    /**
     * @return float[]
     */
    public function getPlaneEquation(): array
    {
        $origem = $this->origin;
        $a = $this->normal->x;
        $b = $this->normal->y;
        $c = $this->normal->z;
        $d = -(($a * $origem->x) + ($b * $origem->y) + ($c * $origem->z));

        return [$a, $b, $c, $d];
    }
}
