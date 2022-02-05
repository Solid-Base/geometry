<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Poligono;

use Solidbase\Geometria\Aplicacao\Interseccao\InterseccaoLinhaCirculo;
use Solidbase\Geometria\Dominio\Circulo;
use Solidbase\Geometria\Dominio\Fabrica\ArcoCirculoFabrica;
use Solidbase\Geometria\Dominio\Fabrica\LinhaFabrica;
use Solidbase\Geometria\Dominio\Fabrica\PolilinhaFabrica;
use Solidbase\Geometria\Dominio\Fabrica\VetorFabrica;
use Solidbase\Geometria\Dominio\Linha;
use Solidbase\Geometria\Dominio\Polilinha;

class AreaCirculoContidoPoligono
{
    public static function executar(Polilinha $polilinha, Circulo $circulo): float
    {
        $polilinha->fecharPolilinha();
        $pontos = $polilinha->pontos();
        $quantidade = count($pontos);
        $pontosIntersecao = [];
        for ($i = 1; $i < $quantidade; ++$i) {
            $p1 = $pontos[$i - 1];
            $p2 = $pontos[$i];
            $linha = LinhaFabrica::apartirDoisPonto($p2, $p1);
            if (!InterseccaoLinhaCirculo::possuiInterseccao($linha, $circulo)) {
                continue;
            }
            $linhaIntersecao = InterseccaoLinhaCirculo::executar($linha, $circulo);
            if ($circulo->pontoInternoCirculo($p1) || $circulo->pontoFronteiraCirculo($p1)) {
                $pontosIntersecao[] = $p1;
            }
            if ($linha->pontoPertenceSegmento($linhaIntersecao->origem)) {
                $pontosIntersecao[] = $linhaIntersecao->origem;
            }
            if ($linha->pontoPertenceSegmento($linhaIntersecao->final)) {
                $pontosIntersecao[] = $linhaIntersecao->final;
            }
            if ($circulo->pontoInternoCirculo($p2) || $circulo->pontoFronteiraCirculo($p2)) {
                $pontosIntersecao[] = $p2;
            }
        }
        if (0 == count($pontosIntersecao)) {
            return 0;
        }
        $primeiro = reset($pontosIntersecao);
        $ultimo = end($pontosIntersecao);
        $direcao = (VetorFabrica::apartirDoisPonto($primeiro, $ultimo))->produtoVetorial(VetorFabrica::BaseZ());
        $linha = new Linha($primeiro->pontoMedio($ultimo), $direcao, 1);
        $linhaIntersecao = InterseccaoLinhaCirculo::executar($linha, $circulo);
        $pontoArco = PontoPertencePoligono::executar($polilinha, $linhaIntersecao->origem) ? $linhaIntersecao->origem : $linhaIntersecao->final;
        $arco = ArcoCirculoFabrica::arcoTresPontos($primeiro, $pontoArco, $ultimo);
        $areaArco = $arco->area();
        $poligonoNovo = PolilinhaFabrica::criarPolilinhaPontos($pontosIntersecao);
        if (count($poligonoNovo) < 3) {
            return $areaArco;
        }
        $propriedade = PropriedadePoligono::executar($poligonoNovo);

        return $propriedade->area + $areaArco;
    }
}
