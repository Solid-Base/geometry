<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Application\Line;

use Solidbase\Geometry\Application\Intersector\LineIntersector;
use Solidbase\Geometry\Domain\Factory\VectorFactory;
use Solidbase\Geometry\Domain\Line;
use Solidbase\Geometry\Domain\Point;

class LinePointSorter
{
    /**
     * Undocumented function.
     *
     * @param Point[] $pontos
     *
     * @return Point[]
     */
    public static function Calculate(Line $linha, array $pontos): array
    {
        $direcao = $linha->direction;
        $direcaoPerpendicular = VectorFactory::CreatePerpendicular($linha->direction);
        $pontosLinha = [];
        $origem = $linha->origin;
        foreach ($pontos as $key => $ponto) {
            $linha1 = new Line($ponto, $direcaoPerpendicular, 1);
            $pontoIntersecao = LineIntersector::Calculate($linha, $linha1);
            $direcaoTeste = VectorFactory::CreateFromPoints($origem, $pontoIntersecao);
            if (!$direcaoTeste->hasSameSense($direcao)) {
                $origem = $pontoIntersecao;
            }
            $pontosLinha[$key] = $pontoIntersecao;
        }
        $retorno = [];
        $distancias = array_map(fn(Point $p) => $p->distanceToPoint($origem), $pontosLinha);
        asort($distancias);
        foreach (array_keys($distancias) as $key) {
            $retorno[] = $pontos[$key];
        }

        return $retorno;
    }
}
