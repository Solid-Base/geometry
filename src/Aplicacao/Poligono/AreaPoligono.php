<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Poligono;

use Solidbase\Geometria\Dominio\Polilinha;
use SolidBase\Matematica\Aritimetica\Numero;

class AreaPoligono
{
    private function __construct()
    {
    }

    public static function executar(Polilinha $poligono): ?Numero
    {
        $soma = numero(0, PRECISAO_SOLIDBASE);
        if (\count($poligono) < 3) {
            return null;
        }
        $poligono->fecharPolilinha();
        $pontos = $poligono->pontos();
        foreach ($pontos as $i => $ponto) {
            if (!isset($pontos[$i + 1])) {
                break;
            }
            $proximo = $pontos[$i + 1];
            $soma->somar(somar($ponto->x, $proximo->x)->multiplicar(subtrair($proximo->y, $ponto->y)));
        }

        $area = dividir($soma, 2);

        if (eZero($area)) {
            return null;
        }

        return $area;
    }
}
