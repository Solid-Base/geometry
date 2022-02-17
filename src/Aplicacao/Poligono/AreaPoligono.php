<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Poligono;

use Solidbase\Geometria\Dominio\Polilinha;

class AreaPoligono
{
    private function __construct()
    {
    }

    public static function executar(Polilinha $poligono): ?float
    {
        $soma = 0;
        if (\count($poligono) <= 3) {
            return null;
        }
        $poligono->fecharPolilinha();
        $pontos = $poligono->pontos();
        foreach ($pontos as $i => $ponto) {
            if (!isset($pontos[$i + 1])) {
                break;
            }
            $proximo = $pontos[$i + 1];
            $soma += ($ponto->x + $proximo->x) * ($proximo->y - $ponto->y);
        }

        $area = $soma / 2;

        if (eZero($area)) {
            return null;
        }

        return $area;
    }
}
