<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Application\Circle;

use DomainException;
use Solidbase\Geometry\Domain\Arc;
use Solidbase\Geometry\Domain\Factory\PolylineFactory;
use Solidbase\Geometry\Domain\Polyline;
use Solidbase\Geometry\Domain\PontoPoligono;

class ConvertArcToPolygon
{
    private function __construct() {}

    public static function execute(Arc $arco, int $numeroDivisao): Polyline
    {
        if ($numeroDivisao <= 3) {
            throw new DomainException('O número de divisão deve ser maior que 0');
        }
        $anguloInicial = $arco->startAngle;
        $anguloFinal = $arco->endAngle;
        if ($anguloFinal < $anguloInicial) {
            $anguloFinal += M_PI * 2;
        }
        $delta = ($anguloFinal - $anguloInicial) / ($numeroDivisao - 1);
        $pontos = [];
        $raio = $arco->radius;
        $centro = $arco->center;
        for ($i = 0; $i < $numeroDivisao; ++$i) {
            $angulo = $anguloInicial + $delta * $i;
            $x = $raio * cos($angulo) + $centro->_x;
            $y = $raio * sin($angulo) + $centro->_y;
            $z = $centro->_z;
            $pontos[] = new PontoPoligono($x, $y, $z);
        }

        return PolylineFactory::CreateFromPoints($pontos);
    }
}
