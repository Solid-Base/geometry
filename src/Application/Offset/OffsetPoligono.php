<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Application\Offset;

use DomainException;
use Solidbase\Geometry\Application\Intersector\LineArcIntersector;
use Solidbase\Geometry\Application\Intersector\LineIntersector;
use Solidbase\Geometry\Application\Offset\Enum\DirecaoOffsetPoligono;
use Solidbase\Geometry\Application\Offset\Enum\DirecaoOffsetReta;
use Solidbase\Geometry\Application\Polygon\ArcoConcordanciaPoligono;
use Solidbase\Geometry\Application\Polygon\PolygonPropertiesCalculator;
use Solidbase\Geometry\Application\Polygon\PolygonTypeEnum;
use Solidbase\Geometry\Application\Points\RotationDirectionEnum;
use Solidbase\Geometry\Application\Points\DetermineRotationDirection;
use Solidbase\Geometry\Domain\Arc;
use Solidbase\Geometry\Domain\Factory\LineFactory;
use Solidbase\Geometry\Domain\Factory\PolylineFactory;
use Solidbase\Geometry\Domain\Line;
use Solidbase\Geometry\Domain\Polyline;
use Solidbase\Geometry\Domain\PointOfPolygon;

class OffsetPoligono
{
    private function __construct() {}

    public static function Generate(float|int $offset, Polyline $polilinha, DirecaoOffsetPoligono $direcao): Polyline
    {
        $poligono = self::ClearPolygon($polilinha);
        $propriedade = PolygonPropertiesCalculator::Calculate($poligono);
        if (PolygonTypeEnum::Concave == $propriedade->type) {
            throw new DomainException('O algoritmo só funciona em polígonos convexos');
        }
        $offsetLinha = DirecaoOffsetReta::tryFrom($propriedade->sense * $direcao->value);

        $numeroPonto = \count($poligono);
        $pontos = $poligono->getPoints();
        $linhas = [];
        for ($i = 1; $i < $numeroPonto; ++$i) {
            /**
             * @var PointOfPolygon
             */
            $p1 = $pontos[$i - 1];

            /**
             * @var PointOfPolygon
             */
            $p2 = $pontos[$i];
            $linha = LineFactory::CreateFromPoints($p2, $p1);
            if (DirecaoOffsetPoligono::Interno == $direcao && sbLessThan($linha->distanceFromPoint($propriedade->center), $offset)) {
                throw new DomainException('Não é possível gerar offset');
            }
            $linhaOffset = OffsetLine::Generate($offset, $linha, $offsetLinha);
            $linhas[] = $linhaOffset;
            if (!sbIsZero($p2->agreement)) {
                $p3 = $pontos->get($i + 1) ?? $pontos->get(0);
                $arco = ArcoConcordanciaPoligono::executar($p2, $p3);
                $arcoNovo = OffsetArc::Generate($offset, $arco, $direcao);
                $p2->setAgreement(0);

                $linhas[] = $arcoNovo;
                ++$i;
            }
        }

        return self::GeneratePolygonOffset($linhas, $polilinha->isPolygon(), RotationDirectionEnum::tryFrom($propriedade->sense));
    }

    private static function ClearPolygon(Polyline $polilinha): Polyline
    {
        $pontos = PolylineFactory::ClearPointsPolygon($polilinha->getPoints());

        return PolylineFactory::CreateFromPoints($pontos, fechado: $polilinha->isPolygon());
    }

    private static function GeneratePolygonOffset(array $linhas, bool $ePoligono, RotationDirectionEnum $rotacao): Polyline
    {
        $numeroLinha = \count($linhas);
        $pontos = [];
        for ($i = 1; $i < $numeroLinha; ++$i) {
            $linha1 = $linhas[$i - 1];
            $linha2 = $linhas[$i];
            if (is_a($linha2, Arc::class)) {
                $pontosArco = self::PointFromArc($linha2, $linha1, $rotacao);
                if (null === $pontosArco) {
                    unset($linhas[$i]);

                    return self::GeneratePolygonOffset(array_values($linhas), $ePoligono, $rotacao);
                }
                $pontos[] = $pontosArco;

                continue;
            }
            if (is_a($linha1, Arc::class)) {
                $pontosArco = self::PointFromArc($linha1, $linha2, $rotacao);
                if (null === $pontosArco) {
                    unset($linhas[$i - 1]);

                    return self::GeneratePolygonOffset(array_values($linhas), $ePoligono, $rotacao);
                }
                $pontosArco->setAgreement(0);
                $pontos[] = $pontosArco;

                continue;
            }

            $ponto = LineIntersector::Calculate($linha1, $linha2);
            $rotacaoNova = DetermineRotationDirection::execute($linha1->origem, $ponto, $linha2->final);
            if ($rotacao != $rotacaoNova) {
                unset($linhas[$i]);

                return self::GeneratePolygonOffset(array_values($linhas), $ePoligono, $rotacao);
            }
            $pontos[] = $ponto;
        }
        if ($ePoligono) {
            $primeiroPonto = LineIntersector::Calculate($linhas[0], $linhas[$numeroLinha - 1]);
            array_unshift($pontos, $primeiroPonto);

            return PolylineFactory::CreateFromPoints($pontos, fechado: true);
        }
        $primeiroPonto = RotationDirectionEnum::CLOCKWISE == $rotacao ? $linhas[0]->origem : $linhas[0]->final;
        $ultimoPonto = RotationDirectionEnum::CLOCKWISE == $rotacao ? $linhas[$numeroLinha - 1]->final : $linhas[$numeroLinha - 1]->origem;
        array_unshift($pontos, $primeiroPonto);
        $pontos[] = $ultimoPonto;

        return PolylineFactory::CreateFromPoints($pontos);
    }

    private static function PointFromArc(Arc $arco, Line $linha, RotationDirectionEnum $rotacao): ?PointOfPolygon
    {
        if (sbIsZero($arco->radius)) {
            return null;
        }
        [$p1] = LineArcIntersector::executar($linha, $arco);
        $angulo = $arco->getAngleTotal();

        $bulge = abs(tan($angulo * 0.25));
        $p1 = new PointOfPolygon($p1->x, $p1->y);
        $p1->setAgreement($bulge * $rotacao->value);

        return $p1;
    }
}
