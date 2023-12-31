<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Application\Circle;

use DomainException;
use Solidbase\Geometry\Domain\Circle;
use Solidbase\Geometry\Domain\Factory\PolylineFactory;
use Solidbase\Geometry\Domain\Polyline;
use Solidbase\Geometry\Domain\PointOfPolygon;

class ConvertCircleToPolygon
{
    private function __construct() {}

    public static function execute(Circle $circulo, int $numeroDivisao): Polyline
    {
        if ($numeroDivisao <= 0) {
            throw new DomainException('O número de divisão deve ser maior que 0');
        }
        $angulo = M_PI * 2 / $numeroDivisao;
        $pontos = [];
        $raio = $circulo->radius;
        $centro = $circulo->center;
        for ($i = 0; $i < $numeroDivisao; ++$i) {
            $x = $raio * cos($angulo * $i) + $centro->x;
            $y = $raio * sin($angulo * $i) + $centro->y;
            $z = $centro->z;
            $pontos[] = new PointOfPolygon($x, $y, $z);
        }

        return PolylineFactory::CreateFromPoints($pontos);
    }
}
