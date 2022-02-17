<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Poligono;

use Solidbase\Geometria\Aplicacao\Pontos\RotacaoPontoEnum;
use Solidbase\Geometria\Aplicacao\Pontos\SentidoRotacaoTresPontos;
use Solidbase\Geometria\Dominio\Fabrica\PolilinhaFabrica;
use Solidbase\Geometria\Dominio\Fabrica\VetorFabrica;
use Solidbase\Geometria\Dominio\Polilinha;
use Solidbase\Geometria\Dominio\Ponto;

class FechoConvexo
{
    public static function executar(array $pontos): Polilinha
    {
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
            $pontos = array_values($pontos);
            $i -= 2;
            --$total;
        }

        return PolilinhaFabrica::criarPolilinhaPontos($pontos, fechado: true);
    }

    private static function pontoInferior(array &$pontos): Ponto
    {
        usort($pontos, fn (Ponto $p1, Ponto $p2) => ($p1->y == $p2->y) ? $p1->x <=> $p2->x : $p1->y <=> $p2->y);
        $pontoRetorno = reset($pontos);
        unset($pontos[0]);

        return $pontoRetorno;
    }

    private static function ordenarPontos(array &$pontos, Ponto $pontoInicial): void
    {
        usort($pontos, fn (Ponto $p1, Ponto $p2) => VetorFabrica::apartirDoisPonto($p1, $pontoInicial)->anguloAbsoluto() <=> VetorFabrica::apartirDoisPonto($p2, $pontoInicial)->anguloAbsoluto());
        array_unshift($pontos, $pontoInicial);
    }
}
