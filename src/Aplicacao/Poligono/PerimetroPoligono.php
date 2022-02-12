<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Poligono;

use Solidbase\Geometria\Dominio\Polilinha;

class PerimetroPoligono
{
    private function __construct()
    {
    }

    public static function executar(Polilinha $poligono): ?float
    {
        $soma = 0;
        if (\count($poligono) < 3) {
            return null;
        }
        $poligono->fecharPolilinha();
        $pontos = $poligono->pontos();
        $quantidade = count($pontos);
        for ($i = 1; $i < $quantidade; ++$i) {
            $p1 = $pontos[$i - 1];
            $p2 = $pontos[$i];
            $soma += ($p1->distanciaParaPonto($p2));
        }

        return $soma;
    }
}
