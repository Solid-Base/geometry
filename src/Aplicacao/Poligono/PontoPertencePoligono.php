<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Poligono;

use Solidbase\Geometria\Aplicacao\Interseccao\InterseccaoLinhas;
use Solidbase\Geometria\Dominio\Fabrica\LinhaFabrica;
use Solidbase\Geometria\Dominio\Fabrica\VetorFabrica;
use Solidbase\Geometria\Dominio\Linha;
use Solidbase\Geometria\Dominio\Polilinha;
use Solidbase\Geometria\Dominio\Ponto;

class PontoPertencePoligono
{
    private function __construct()
    {
    }

    public static function executar(Polilinha $poligono, Ponto $ponto): bool
    {
        $poligono->fecharPolilinha();
        $linhaComparacao = new Linha($ponto, VetorFabrica::BaseX(), 1);
        $numeroPontos = \count($poligono);
        $contagem = 0;
        $pontos = $poligono->pontos();
        for ($i = 0; $i < $numeroPontos - 1; ++$i) {
            $p1 = $pontos[$i];
            $p2 = $pontos[$i + 1];
            $linha = LinhaFabrica::apartirDoisPonto($p1, $p2);
            if ($linha->pontoPertenceLinha($ponto)) {
                continue;
            }
            if ($linha->eParelo($linhaComparacao)) {
                continue;
            }
            $pontoIntersecao = InterseccaoLinhas::executar($linha, $linhaComparacao);
            if ($pontoIntersecao->x < $ponto->x) {
                continue;
            }
            if (eZero($pontoIntersecao->distanciaParaPonto($p1))
            || eZero($pontoIntersecao->distanciaParaPonto($p2))) {
                if ((eZero(subtrair($pontoIntersecao->y, $p1->y))) && $pontoIntersecao->y->eMaior($p2->y)) {
                    ++$contagem;
                }
                if ((eZero(subtrair($pontoIntersecao->y, $p2->y))) && $pontoIntersecao->y->eMaior($p1->y)) {
                    ++$contagem;
                }

                continue;
            }
            if (!$linha->pontoPertenceSegmento($pontoIntersecao)) {
                continue;
            }
            ++$contagem;
        }

        return 1 === $contagem % 2;
    }
}
