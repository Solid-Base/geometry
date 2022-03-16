<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Poligono;

use Solidbase\Geometria\Aplicacao\Pontos\RotacaoPontoEnum;
use Solidbase\Geometria\Aplicacao\Pontos\SentidoRotacaoTresPontos;
use Solidbase\Geometria\Colecao\ColecaoPontos;
use Solidbase\Geometria\Dominio\Fabrica\PolilinhaFabrica;
use Solidbase\Geometria\Dominio\Fabrica\VetorFabrica;
use Solidbase\Geometria\Dominio\Polilinha;
use Solidbase\Geometria\Dominio\Ponto;

class FechoConvexo
{
    public static function executar(ColecaoPontos $pontos): Polilinha
    {
        $pontos = $pontos->unique();
        if (count($pontos) <= 3) {
            return PolilinhaFabrica::criarPolilinhaPontos($pontos, fechado: true);
        }
        $pontoInicial = self::pontoInferior($pontos);
        self::ordenarPontos($pontos, $pontoInicial);
        $total = count($pontos);
        for ($i = 2; $i < $total; ++$i) {
            $p1 = $pontos[$i - 2];
            $p2 = $pontos[$i - 1];
            $p3 = $pontos[$i];

            $sentido = SentidoRotacaoTresPontos::executar($p1, $p2, $p3);
            if (RotacaoPontoEnum::ANTI_HORARIO == $sentido) {
                continue;
            }

            unset($pontos[$i - 1]);
            $pontos->enumerarIndices();
            $i -= $i > 3 ? 2 : 1;
            --$total;
        }

        return PolilinhaFabrica::criarPolilinhaPontos($pontos, fechado: true);
    }

    private static function pontoInferior(ColecaoPontos &$pontos): Ponto
    {
        $retorno = $pontos->array();
        usort($retorno, fn (Ponto $p1, Ponto $p2) => ($p1->y == $p2->y) ? $p1->x <=> $p2->x : $p1->y <=> $p2->y);
        $pontoRetorno = reset($retorno);
        unset($retorno[0]);
        $pontos = ColecaoPontos::deArray($retorno);

        return $pontoRetorno;
    }

    private static function ordenarPontos(ColecaoPontos &$pontos, Ponto $pontoInicial): void
    {
        $array = $pontos->array();
        usort($array, fn (Ponto $p1, Ponto $p2) => VetorFabrica::apartirDoisPonto($p1, $pontoInicial)->anguloAbsoluto() <=> VetorFabrica::apartirDoisPonto($p2, $pontoInicial)->anguloAbsoluto());
        array_unshift($array, $pontoInicial);
        $pontos = ColecaoPontos::deArray($array);
    }
}
