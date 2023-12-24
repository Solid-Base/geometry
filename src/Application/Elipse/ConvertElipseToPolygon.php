<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Application\Elipse;

use Solidbase\Geometry\Domain\Elipse;
use Solidbase\Geometry\Domain\Factory\PolylineFactory;
use Solidbase\Geometry\Domain\Polyline;
use Solidbase\Geometry\Domain\Point;

class ConvertElipseToPolygon
{
    public static function execute(Elipse $elipse, int $numDivisao): Polyline
    {
        $pi = M_PI;
        $angulo = 2 * $pi / $numDivisao;
        $raio = $elipse->largestRadius;
        $pontos = [];
        for ($i = 0; $i < $numDivisao; ++$i) {
            $anguloI = $i * $angulo;
            $x = $raio * cos($anguloI);
            $y = self::calcularOrdenadaY($elipse, $x);
            if ($anguloI > $pi) {
                $y = $y * (-1);
            }
            $ponto = new Point($x, $y);
            $pontos[$i] = $ponto->add($elipse->center);
        }

        $retorno = PolylineFactory::CreateFromPoints($pontos, close: true);
        $angulo = $elipse->direction->getAbsoluteAngle();
        if (!sbIsZero($angulo)) {
            $retorno->rotate($angulo);
        }

        return $retorno;
    }

    private static function calcularOrdenadaY(Elipse $elipse, float $x): float
    {
        $a = $elipse->largestRadius;
        $b = $elipse->minorRadius;

        return sqrt((1 - ($x ** 2 / $a ** 2)) * ($b ** 2));
    }
}
