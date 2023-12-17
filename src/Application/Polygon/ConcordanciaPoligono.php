<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Application\Polygon;

use DomainException;
use Solidbase\Geometry\Application\Points\RotationDirectionEnum;
use Solidbase\Geometry\Application\Points\DetermineRotationDirection;
use Solidbase\Geometry\Domain\Factory\VectorFactory;
use Solidbase\Geometry\Domain\PontoPoligono;

class ConcordanciaPoligono
{
    public static function executar(PontoPoligono $p1, PontoPoligono $p2, PontoPoligono $p3, float|int $raio): array
    {
        $sentido = DetermineRotationDirection::execute($p1, $p2, $p3);
        if (RotationDirectionEnum::COLLINEAR == $sentido) {
            throw new DomainException('Não é possível fazer concordancia de pontos alinhados!');
        }
        $v1 = VectorFactory::CreateFromPoints($p2, $p1)->getUnitary();
        $v2 = VectorFactory::CreateFromPoints($p2, $p3)->getUnitary();
        $angulo = $v1->getAngle($v2);
        $comprimento2 = ($raio / tan($angulo / 2));
        $p1Retorno = $p2->add($v1->scalar($comprimento2));
        $p2Retorno = $p2->add($v2->scalar($comprimento2));
        $anguloBulge = M_PI - $angulo;

        $bulge = tan($anguloBulge * 0.25) * ($sentido->value);
        $p1Retorno->setAgreement($bulge);

        return [$p1Retorno, $p2Retorno];
    }
}
