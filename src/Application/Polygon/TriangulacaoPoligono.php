<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Application\Polygon;

use Solidbase\Geometry\Application\Intersector\LineIntersector;
use Solidbase\Geometry\Domain\Factory\LineFactory;
use Solidbase\Geometry\Domain\Factory\PolylineFactory;
use Solidbase\Geometry\Domain\Factory\VectorFactory;
use Solidbase\Geometry\Domain\Polyline;
use Solidbase\Geometry\Domain\Point;

class TriangulacaoPoligono
{
    private array $triangulos = [];

    public function __construct(private Polyline $poligono)
    {
        $poligono->closePolyline();
    }

    public function triangular(Polyline $poligono): void
    {
        $quantidade = \count($poligono);
        if ($quantidade <= 3) {
            return;
        }
        $pontos = $poligono->getPoints();
        for ($i = 2; $i < $quantidade; ++$i) {
            $p1 = $pontos->get($i - 2);
            $p2 = $pontos->get($i - 1);
            $p3 = $pontos->get($i);
            // $orientacao = $this->orientacao2D($p1, $p2, $p3);
            $angulo = $this->anguloPontos($p1, $p2, $p3);
            if ($angulo > M_PI) {
                continue;
            }
            if ($this->segmentoIntercepta($p1, $p3, $poligono, $i)) {
                continue;
            }
            unset($pontos[$i - 1]);
            $this->triangulos[] = [$p1, $p2, $p3];

            $poligono = PolylineFactory::CreateFromPoints($pontos);

            $this->triangular($poligono);

            return;
        }
    }

    private function segmentoIntercepta(Point $p1, Point $p3, Polyline $poligono, int $key): bool
    {
        $linha = LineFactory::CreateFromPoints($p1, $p3);
        $pontos = $poligono->getPoints();
        $quantidade = \count($poligono);
        for ($i = $key; $i < $quantidade; ++$i) {
            $pl1 = $pontos->get($i - 1);
            $pl2 = $pontos->get($i);
            $linhaL = LineFactory::CreateFromPoints($pl1, $pl2);
            if ($linhaL->isParallel($linha)) {
                continue;
            }

            $pontoIntersecao = LineIntersector::Calculate($linha, $linhaL);
            if ($pontoIntersecao->isEquals($p1) || $pontoIntersecao->isEquals($p3)) {
                continue;
            }
            if ($linha->belongsToSegment($pontoIntersecao) && $linhaL->belongsToSegment($pontoIntersecao)) {
                return true;
            }
        }

        return false;
    }

    private function anguloPontos(Point $p1, Point $p2, Point $p3): float
    {
        $vetor1 = VectorFactory::CreateFromPoints($p1, $p2);
        $vetor2 = VectorFactory::CreateFromPoints($p2, $p3);

        return $vetor1->getAngle($vetor2);
    }

    private function orientacao2D(Point $p1, Point $p2, Point $p3): int
    {
        $vetor1 = VectorFactory::CreateFromPoints($p1, $p2);
        $vetor2 = VectorFactory::CreateFromPoints($p2, $p3);
        $valor = $vetor1->crossProduct($vetor2);

        if ($valor->z < 0) {
            return -1;
        }
        if ($valor->z > 0) {
            return 1;
        }

        return 0;
    }
}
