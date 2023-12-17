<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Application\Polygon;

use Solidbase\Geometry\Domain\Factory\PolylineFactory;
use Solidbase\Geometry\Domain\Polyline;

class PolygonFilletApplier
{
    public static function executar(float $raio, int $indiceVertice, Polyline $polilinha): Polyline
    {
        if (!sbBetween(0, $indiceVertice, count($polilinha) - 1)) {
            return $polilinha;
        }
        $pontos = $polilinha->getPoints();
        $pontosNovos = [];
        for ($i = 0; $i < count($polilinha); ++$i) {
            if ($i == $indiceVertice) {
                $p1 = $pontos->get($i - 1);
                $p2 = $pontos->get($i);
                $p3 = $pontos->get($i + 1);
                $pontosConcordancia = ConcordanciaPoligono::executar($p1, $p2, $p3, $raio);
                $pontosNovos[] = $pontosConcordancia[0];
                $pontosNovos[] = $pontosConcordancia[1];

                continue;
            }
            $pontosNovos[] = $pontos[$i];
        }

        return PolylineFactory::CreateFromPoints($pontosNovos, fechado: $polilinha->isPolygon());
    }
}
