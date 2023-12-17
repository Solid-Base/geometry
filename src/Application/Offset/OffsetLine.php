<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Application\Offset;

use Solidbase\Geometry\Application\Offset\Enum\DirecaoOffsetReta;
use Solidbase\Geometry\Domain\Factory\LineFactory;
use Solidbase\Geometry\Domain\Factory\VectorFactory;
use Solidbase\Geometry\Domain\Line;

class OffsetLine
{
    private function __construct() {}

    public static function Generate(float|int $offset, Line $linha, DirecaoOffsetReta $direcao): Line
    {
        $perpendicular = VectorFactory::CreatePerpendicular($linha->_direction)->getUnitary();
        $origem = $linha->origin->add($perpendicular->scalar($offset * $direcao->value));
        $final = $linha->end->add($perpendicular->scalar($offset * $direcao->value));

        return LineFactory::CreateFromPoints($origem, $final);
    }
}
