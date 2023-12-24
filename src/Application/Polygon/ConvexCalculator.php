<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Application\Polygon;

use Solidbase\Geometry\Application\Points\RotationDirectionEnum;
use Solidbase\Geometry\Application\Points\DetermineRotationDirection;
use Solidbase\Geometry\Collection\PointCollection;
use Solidbase\Geometry\Domain\Factory\PolylineFactory;
use Solidbase\Geometry\Domain\Factory\VectorFactory;
use Solidbase\Geometry\Domain\Polyline;
use Solidbase\Geometry\Domain\Point;

class ConvexCalculator
{
    public static function Calculate(PointCollection $pontos): Polyline
    {
        $pontos = $pontos->unique();
        if (count($pontos) <= 3) {
            return PolylineFactory::CreateFromPoints($pontos, close: true);
        }
        $pontoInicial = self::pontoInferior($pontos);
        self::ordenarPontos($pontos, $pontoInicial);
        $total = count($pontos);
        for ($i = 2; $i < $total; ++$i) {
            $p1 = $pontos->get($i - 2);
            $p2 = $pontos->get($i - 1);
            $p3 = $pontos->get($i);

            $sentido = DetermineRotationDirection::execute($p1, $p2, $p3);
            if (RotationDirectionEnum::Counterclockwise == $sentido) {
                continue;
            }

            unset($pontos[$i - 1]);
            $pontos = new PointCollection($pontos->getValues());
            $i -= $i > 3 ? 2 : 1;
            --$total;
        }

        return PolylineFactory::CreateFromPoints($pontos, close: true);
    }

    private static function pontoInferior(PointCollection &$pontos): Point
    {
        $retorno = $pontos->toArray();
        usort($retorno, fn(Point $p1, Point $p2) => ($p1->y == $p2->y) ? $p1->x <=> $p2->x : $p1->y <=> $p2->y);
        $pontoRetorno = reset($retorno);
        unset($retorno[0]);
        $pontos = new PointCollection($retorno);

        return $pontoRetorno;
    }

    private static function ordenarPontos(PointCollection &$pontos, Point $pontoInicial): void
    {
        $array = $pontos->toArray();
        usort($array, fn(Point $p1, Point $p2) => VectorFactory::CreateFromPoints($p1, $pontoInicial)->getAbsoluteAngle() <=> VectorFactory::CreateFromPoints($p2, $pontoInicial)->getAbsoluteAngle());
        array_unshift($array, $pontoInicial);
        $pontos =  new PointCollection($array);
    }
}
