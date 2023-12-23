<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Application\Line;

use Solidbase\Geometry\Domain\Factory\VectorFactory;
use Solidbase\Geometry\Domain\Line;
use Solidbase\Geometry\Domain\Point;

class PosicaoPontoLinha
{
    public static function executar(Line $linha, Point $ponto): PointRelationToLineEnum
    {
        if ($linha->belongsToLine($ponto)) {
            return PointRelationToLineEnum::On;
        }

        $origem = $linha->origin;
        $direcao = $linha->direction;
        $vetor1 = VectorFactory::CreateFromPoints($origem, $ponto);
        $resultado = $direcao->produtoVetorial($vetor1);
        if (sbBiggerThen($resultado->z, 0)) {
            return PointRelationToLineEnum::Left;
        }

        return PointRelationToLineEnum::Right;
    }
}
