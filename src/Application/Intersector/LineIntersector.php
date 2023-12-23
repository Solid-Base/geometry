<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Application\Intersector;

use Solidbase\Geometry\Domain\Factory\VectorFactory;
use Solidbase\Geometry\Domain\Line;
use Solidbase\Geometry\Domain\Point;

class LineIntersector
{
    private function __construct() {}

    public static function Calculate(Line $linha1, Line $linha2): ?Point
    {
        if ($linha1->isParallel($linha2)) {
            return null;
        }
        if (!$linha1->isCoplanar($linha2)) {
            return null;
        }
        if ($linha1->origin->isEquals($linha2->origin)) {
            return $linha1->origin;
        }
        if ($linha1->end->isEquals($linha2->end)) {
            return $linha1->end;
        }
        [$s,] = self::calcularTS($linha1, $linha2);

        return $linha1->origin->add($linha1->direction->scalar($s));
    }

    /**
     * @return float[]
     */
    private static function calcularTS(Line $linha1, Line $linha2): array
    {
        $k = $linha1->origin;
        $l = $linha1->pointAtLength(1);
        $m = $linha2->origin;
        $n = $linha2->pointAtLength(1);

        $diretorS = VectorFactory::CreateFromPoints($k, $l);
        $diretorR = VectorFactory::CreateFromPoints($n, $m);

        $determinante = $diretorR->crossProduct($diretorS);

        $diretorMk = VectorFactory::CreateFromPoints($k, $m);
        $vetorialRMk = $diretorR->crossProduct($diretorMk);
        $vetorialSMk = $diretorS->crossProduct($diretorMk);
        if ((!self::retaPertenceOx($linha1) || !self::retaPertenceOx($linha2))
        && (!self::retaPertenceOy($linha1) || !self::retaPertenceOy($linha2)) && !sbIsZero($determinante->z)) {
            $s = ($vetorialRMk->z / $determinante->z);
            $t = ($vetorialSMk->z / $determinante->z);

            return [$s, $t];
        }
        if ((!self::retaPertenceOx($linha1) || !self::retaPertenceOx($linha2))
        && (!self::retaPertenceOz($linha1) || !self::retaPertenceOz($linha2)) && !sbIsZero($determinante->y)) {
            $s = ($vetorialRMk->y / $determinante->y);
            $t = ($vetorialSMk->y / $determinante->y);

            return [$s, $t];
        }
        $s = ($vetorialRMk->x / $determinante->x);
        $t = ($vetorialSMk->x / $determinante->x);

        return [$s, $t];
    }

    private static function retaPertenceOx(Line $linha): bool
    {
        $direcao = $linha->direction->getUnitary();

        return sbEquals($direcao->x, 1);
    }

    private static function retaPertenceOy(Line $linha): bool
    {
        $direcao = $linha->direction->getUnitary();

        return sbEquals($direcao->y, 1);
    }

    private static function retaPertenceOz(Line $linha): bool
    {
        $direcao = $linha->direction->getUnitary();

        return sbEquals($direcao->z, 1);
    }
}
