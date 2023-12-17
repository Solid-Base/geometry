<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Application\Intersector;

use Solidbase\Geometry\Domain\Line;
use Solidbase\Geometry\Domain\Plane;
use Solidbase\Geometry\Domain\Point;
use SolidBase\Math\Algebra\Decomposition\LowerUpper;
use SolidBase\Math\Algebra\Matriz;

class PlaneIntersector
{
    public static function Calculate(Plane $plano1, Plane $plano2): ?Line
    {
        if ($plano1->normal->hasSameSense($plano2->normal)) {
            return null;
        }
        $direcaoReta = $plano1->normal->crossProduct($plano2->normal);
        $plano3 = new Plane($plano1->origin, $direcaoReta);
        [$x1, $y1, $z1, $d1] = $plano1->getPlaneEquation();
        [$x2, $y2, $z2, $d2] = $plano2->getPlaneEquation();
        [$x3, $y3, $z3, $d3] = $plano3->getPlaneEquation();
        $matriz = new Matriz([[$x1, $y1, $z1], [$x2, $y2, $z2], [$x3, $y3, $z3]]);
        $decomposicao = LowerUpper::Decompose($matriz);
        if (sbIsZero($decomposicao->Determinant())) {
            return null;
        }
        $matrizPonto = $decomposicao->SolveSystem(new Matriz([[-$d1], [-$d2], [-$d3]]));
        $ponto = new Point($matrizPonto['1'], $matrizPonto['2'], $matrizPonto['3']);

        return new Line($ponto, $direcaoReta, 1);
    }
}
