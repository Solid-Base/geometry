<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Application\Polygon;

use Solidbase\Geometry\Domain\Polyline;
use Solidbase\Geometry\Domain\Point;
use SolidBase\Math\Geometry\Polygon;

class PolygonPropertiesCalculator
{
    private function __construct(private Polyline $poligono) {}

    public static function Calculate(Polyline $poligono): ?DataPolygon
    {
        $poligono = clone $poligono;
        $tipo = PolygonTypeChecker::Check($poligono);
        $area = PolygonAreaCalculator::Calculate($poligono);
        if (null === $area) {
            return null;
        }
        $sentido = $area > 0 ? 1 : -1;
        $area = sbModule($area);
        $centro = PolygonCenterCalculator::calculate($poligono);

        [$ix,$iy] = SecondMomentOfInertiaCalculator::Calculate($poligono);

        $momentoInerciaX = ($ix * $sentido);
        $momentoInerciaY = ($iy * $sentido);

        if ($centro->isEquals(new Point())) {
            $momentoInerciaPrincipalX = ($ix * $sentido);
            $momentoInerciaPrincipalY = ($iy * $sentido);

            return self::montarRetorno(
                $area,
                (int) $sentido,
                $centro,
                $tipo,
                $momentoInerciaX,
                $momentoInerciaY,
                $momentoInerciaPrincipalX,
                $momentoInerciaPrincipalY
            );
        }
        $poligono = clone $poligono;
        $poligono->move(-$centro->x, -$centro->y);

        [$ix,$iy] = SecondMomentOfInertiaCalculator::Calculate($poligono);
        $momentoInerciaPrincipalX = ($ix * $sentido);
        $momentoInerciaPrincipalY = ($iy * $sentido);

        return self::montarRetorno(
            $area,
            (int) $sentido,
            $centro,
            $tipo,
            $momentoInerciaX,
            $momentoInerciaY,
            $momentoInerciaPrincipalX,
            $momentoInerciaPrincipalY
        );
    }

    private static function montarRetorno(
        float $area,
        int $sentido,
        Point $centro,
        PolygonTypeEnum $tipo,
        float $momentoInerciaX,
        float $momentoInerciay,
        float $momentoInerciaPrincipalX,
        float $momentoInerciaPrincipalY
    ): DataPolygon {
        return new DataPolygon(
            $area,
            $sentido,
            $tipo,
            $centro,
            $momentoInerciaX,
            $momentoInerciay,
            $momentoInerciaPrincipalX,
            $momentoInerciaPrincipalY
        );
    }
}
