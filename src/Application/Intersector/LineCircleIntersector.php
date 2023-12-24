<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Application\Intersector;

use DomainException;
use Exception;
use Solidbase\Geometry\Domain\Circle;
use Solidbase\Geometry\Domain\Factory\VectorFactory;
use Solidbase\Geometry\Domain\Line;
use Solidbase\Geometry\Domain\Point;

class LineCircleIntersector
{
    private function __construct() {}

    public static function tryCalculate(Line $line, Circle $circle): ?array
    {
        try {
            return self::calculate($line, $circle);
        } catch(Exception) {
            return null;
        }
    }

    public static function Calculate(Line $linha, Circle $circulo): array
    {
        if (!self::CheckLineCircleIntersection($linha, $circulo)) {
            throw new DomainException("Line not intersect Circle");
        }
        $distancia = self::distanceCenterFromLine($linha, $circulo->center);
        $comprimento = sbIsZero($circulo->radius - $distancia) ?
                        0 :
                        sqrt($circulo->radius ** 2 - $distancia ** 2);

        $pontoIntersecao = self::GetIntersect($linha, $circulo);
        $direcaoLinha = $linha->direction;
        $ponto1 = $pontoIntersecao->add($direcaoLinha->scalar($comprimento));
        if (sbIsZero($comprimento)) {
            return [$ponto1];
        }
        $ponto2 = $pontoIntersecao->add($direcaoLinha->scalar(-$comprimento));

        return [$ponto1, $ponto2];
    }

    public static function CheckLineCircleIntersection(Line $linha, Circle $circulo): bool
    {
        $distancia = $linha->distanceFromPoint($circulo->center);
        $igual = sbIsZero(($distancia - $circulo->radius));

        return sbLessThan($distancia, $circulo->radius) || $igual;
    }

    private static function GetIntersect(Line $linha, Circle $circulo): Point
    {
        $perpendicular = VectorFactory::CreatePerpendicular($linha->direction);
        $linhaPerpendicular = new Line($circulo->center, $perpendicular, 1);

        return LineIntersector::Calculate($linha, $linhaPerpendicular);
    }

    private static function distanceCenterFromLine(Line $linha, Point $ponto): float
    {
        return $linha->distanceFromPoint($ponto);
    }
}
