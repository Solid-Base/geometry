<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Application\Circle;

use DomainException;
use Solidbase\Geometry\Domain\Arc;
use Solidbase\Geometry\Domain\Factory\PolylineFactory;
use Solidbase\Geometry\Domain\Polyline;
use Solidbase\Geometry\Domain\PointOfPolygon;

class ConvertArcToPolygon
{
    private function __construct() {}

    public static function execute(Arc $arc, int $numeroDivisao): Polyline
    {
        if ($numeroDivisao < 4) {
            throw new DomainException('O número de divisão deve ser maior que 3');
        }
        $anguloInicial = $arc->startAngle;
        $anguloFinal = $arc->endAngle;
        if ($anguloFinal < $anguloInicial) {
            $anguloFinal += M_PI * 2;
        }
        $delta = ($anguloFinal - $anguloInicial) / ($numeroDivisao - 1);
        $pontos = [];
        $raio = $arc->radius;
        $centro = $arc->center;
        for ($i = 0; $i < $numeroDivisao; ++$i) {
            $angulo = $anguloInicial + $delta * $i;
            $x = $raio * cos($angulo) + $centro->x;
            $y = $raio * sin($angulo) + $centro->y;
            $z = $centro->z;
            $pontos[] = new PointOfPolygon($x, $y, $z);
        }

        return PolylineFactory::CreateFromPoints($pontos);
    }
}
