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

        return $linha1->origin->add($linha1->_direction->escalar($s));
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
        && (!self::retaPertenceOy($linha1) || !self::retaPertenceOy($linha2)) && !sbIsZero($determinante->_z)) {
            $s = ($vetorialRMk->_z / $determinante->_z);
            $t = ($vetorialSMk->_z / $determinante->_z);

            return [$s, $t];
        }
        if ((!self::retaPertenceOx($linha1) || !self::retaPertenceOx($linha2))
        && (!self::retaPertenceOz($linha1) || !self::retaPertenceOz($linha2)) && !sbIsZero($determinante->_y)) {
            $s = ($vetorialRMk->_y / $determinante->_y);
            $t = ($vetorialSMk->_y / $determinante->_y);

            return [$s, $t];
        }
        $s = ($vetorialRMk->_x / $determinante->_x);
        $t = ($vetorialSMk->_x / $determinante->_x);

        return [$s, $t];
    }

    private static function retaPertenceOx(Line $linha): bool
    {
        $direcao = $linha->_direction->vetorUnitario();

        return sbEquals($direcao->_x, 1);
    }

    private static function retaPertenceOy(Line $linha): bool
    {
        $direcao = $linha->_direction->vetorUnitario();

        return sbEquals($direcao->_y, 1);
    }

    private static function retaPertenceOz(Line $linha): bool
    {
        $direcao = $linha->_direction->vetorUnitario();

        return sbEquals($direcao->_z, 1);
    }
}
