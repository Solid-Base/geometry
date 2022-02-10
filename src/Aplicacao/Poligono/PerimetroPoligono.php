<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Poligono;

use Solidbase\Geometria\Dominio\Polilinha;
use SolidBase\Matematica\Aritimetica\Numero;

class PerimetroPoligono
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
        $quantidade = count($pontos);
        for ($i = 1; $i < $quantidade; ++$i) {
            $p1 = $pontos[$i - 1];
            $p2 = $pontos[$i];
            $soma->somar($p1->distanciaParaPonto($p2));
        }

        return $soma;
    }
}
