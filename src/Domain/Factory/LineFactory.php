<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Domain\Factory;

use DomainException;
use Solidbase\Geometry\Domain\Line;
use Solidbase\Geometry\Domain\Point;
use Solidbase\Geometry\Domain\Vector;

class LineFactory
{
    public static function CreateFromPoints(Point $ponto1, Point $ponto2): Line
    {
        $vetor = VectorFactory::CreateFromPoints($ponto1, $ponto2);
        if ($vetor->isZero()) {
            $vetor = VectorFactory::CreateBaseX();
        }
        $comprimento = $ponto2->distanceToPoint($ponto1);

        return new Line($ponto1, $vetor->getUnitary(), $comprimento);
    }

    public static function CreateFromOriginAndDirection(Point $origem, Vector $direcao): Line
    {
        return new Line($origem, $direcao, 1);
    }

    public static function CreateFromLineEquation(?float $coeficienteAngular, float $coeficienteLinear): Line
    {
        if (null === $coeficienteAngular) {
            return new Line(new Point($coeficienteLinear, 0), VectorFactory::CreateBaseY(), 1);
        }
        if (sbIsZero($coeficienteAngular)) {
            return new Line(new Point(0, $coeficienteLinear), VectorFactory::CreateBaseX(), 1);
        }
        $p1 = new Point(0, $coeficienteLinear);
        $p2 = new Point(1, ($coeficienteAngular + $coeficienteLinear));

        return self::CreateFromPoints($p1, $p2);
    }
    public static function CreateFromLineEquationFull(float $coeficienteA, float $coeficienteB, float $coeficienteC): Line
    {
        if($coeficienteA === 0 || $coeficienteB === 0) {
            throw new DomainException("Os coeficiente A e B deve ser diferente de 0");
        }
        $coeficienteAngular = -$coeficienteA / $coeficienteB;
        $coeficienteLinear = -$coeficienteC / $coeficienteB;
        return self::CreateFromLineEquation($coeficienteAngular, $coeficienteLinear);
    }
}
