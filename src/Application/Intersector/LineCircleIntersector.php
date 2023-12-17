<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Application\Intersector;

use Solidbase\Geometry\Domain\Circle;
use Solidbase\Geometry\Domain\Factory\VectorFactory;
use Solidbase\Geometry\Domain\Line;
use Solidbase\Geometry\Domain\Point;

class LineCircleIntersector
{
    private function __construct() {}

    public static function Calculate(Line $linha, Circle $circulo): ?array
    {
        if (!self::CheckLineCircleIntersection($linha, $circulo)) {
            return null;
        }
        $distancia = self::distanceCenterFromLine($linha, $circulo->center);
        $comprimento = sbIsZero($circulo->radius - $distancia) ?
                        0 :
                        sqrt($circulo->radius ** 2 - $distancia ** 2);

        $pontoIntersecao = self::GetIntersect($linha, $circulo);
        $direcaoLinha = $linha->_direction;
        $ponto1 = $pontoIntersecao->add($direcaoLinha->escalar($comprimento));
        if (sbIsZero($comprimento)) {
            return [$ponto1];
        }
        $ponto2 = $pontoIntersecao->add($direcaoLinha->escalar(-$comprimento));

        return [$ponto1, $ponto2];
    }

    public static function CheckLineCircleIntersection(Line $linha, Circle $circulo): bool
    {
        $distancia = $linha->distanceFromPoint($circulo->center);
        $igual = sbIsZero(($distancia - $circulo->radius));

        return sbLessThan($distancia, $circulo->radius) || $igual;
    }

    // public function executarOld(): ?Linha
    // {
    //     if (!$this->possuiInterseccao()) {
    //         return null;
    //     }
    //     $distancia = $this->distanciaCentroLinha();
    //     $comprimento = sqrt($this->circulo->raio ** 2 - $distancia ** 2);
    //     $pontoIntersecao = $this->pontoIntersecao();
    //     $direcaoLinha = $this->linha->direcao;
    //     $ponto1 = $pontoIntersecao->somar($direcaoLinha->escalar($comprimento));
    //     $ponto2 = $pontoIntersecao->somar($direcaoLinha->escalar(-$comprimento));

    //     return LinhaFabrica::apartirDoisPonto($ponto1, $ponto2);
    // }

    private static function GetIntersect(Line $linha, Circle $circulo): Point
    {
        $perpendicular = VectorFactory::CreatePerpendicular($linha->_direction);
        $linhaPerpendicular = new Line($circulo->center, $perpendicular, 1);

        return LineIntersector::Calculate($linha, $linhaPerpendicular);
    }

    private static function distanceCenterFromLine(Line $linha, Point $ponto): float
    {
        return $linha->distanceFromPoint($ponto);
    }
}
