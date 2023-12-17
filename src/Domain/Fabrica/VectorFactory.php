<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Domain\Factory;

use DomainException;
use Solidbase\Geometry\Domain\Point;
use Solidbase\Geometry\Domain\Vector;

final class VectorFactory
{
    public static function FromPoint(Point $ponto): Vector
    {
        return new Vector($ponto->_x, $ponto->_y, $ponto->_z);
    }

    public static function CreateFromPoints(Point $ponto1, Point $ponto2): Vector
    {
        $ponto = $ponto2->difference($ponto1);

        return self::FromPoint($ponto);
    }

    public static function CreateBaseX(): Vector
    {
        return new Vector(1, 0, 0);
    }

    public static function CreateBaseY(): Vector
    {
        return new Vector(0, 1, 0);
    }

    public static function CreateBaseZ(): Vector
    {
        return new Vector(0, 0, 1);
    }

    public static function CreatePerpendicular(Vector $vetor): Vector
    {
        if ($vetor->isZero()) {
            throw new DomainException('Não é possível gerar vetor perpendiculares a partir de vetores nulos');
        }
        $baseZ = self::CreateBaseZ();
        if ($vetor->hasSameDirection($baseZ)) {
            return self::CreateBaseX();
        }

        return $vetor->crossProduct($baseZ);
    }

    public static function CreateFromDirectionAndModule(float $angulo, float $modulo = 1): Vector
    {
        $x = cos($angulo) * $modulo;
        $y = sin($angulo) * $modulo;

        return new Vector($x, $y);
    }
}
