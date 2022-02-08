<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Poligono;

use Solidbase\Geometria\Aplicacao\Pontos\SentidoRotacaoTresPontos;
use Solidbase\Geometria\Dominio\Polilinha;

class TipoPoligono
{
    private function __construct()
    {
    }

    public static function executar(Polilinha $poligono): TipoPoligonoEnum
    {
        if (\count($poligono) < 3) {
            return TipoPoligonoEnum::NaoConvexo;
        }
        $pontos = $poligono->pontos();
        $quantidade = count($poligono);
        $orientacaoOriginal = null;
        for ($i = 2; $i < $quantidade; ++$i) {
            $p1 = $pontos[$i - 2];
            $p2 = $pontos[$i - 1];
            $p3 = $pontos[$i];
            $orientacao = SentidoRotacaoTresPontos::executar($p1, $p2, $p3);
            $orientacaoOriginal ??= $orientacao;
            if ($orientacao !== $orientacaoOriginal) {
                return TipoPoligonoEnum::NaoConvexo;
            }
        }

        return TipoPoligonoEnum::Convexo;
    }
}
