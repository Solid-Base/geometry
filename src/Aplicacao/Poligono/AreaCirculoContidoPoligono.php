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
use Solidbase\Geometria\Dominio\Ponto;

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
            $origem = $linhaIntersecao[0];
            $final = $linhaIntersecao[1] ?? $origem;
            if ($circulo->pontoInternoCirculo($p1) || $circulo->pontoFronteiraCirculo($p1)) {
                $pontosIntersecao[] = $p1;
            }
            if ($linha->pontoPertenceSegmento($origem)) {
                $pontosIntersecao[] = $origem;
            }
            if ($linha->pontoPertenceSegmento($final)) {
                $pontosIntersecao[] = $final;
            }
            if ($circulo->pontoInternoCirculo($p2) || $circulo->pontoFronteiraCirculo($p2)) {
                $pontosIntersecao[] = $p2;
            }
        }
        if (0 == count($pontosIntersecao)) {
            return 0;
        }
        $primeiro = self::primeiroCruzarCirculo($pontosIntersecao, $circulo);
        $ultimo = self::ultimoCruzarCirculo($pontosIntersecao, $circulo);
        $direcao = (VetorFabrica::apartirDoisPonto($primeiro, $ultimo))->produtoVetorial(VetorFabrica::BaseZ());
        $linha = new Linha($primeiro->pontoMedio($ultimo), $direcao, 1);
        $linhaIntersecao = InterseccaoLinhaCirculo::executar($linha, $circulo);
        $origem = $linhaIntersecao[0];
        $final = $linhaIntersecao[1] ?? $origem;
        $pontoArco = PontoPertencePoligono::executar($polilinha, $origem) ? $origem : $final;
        $arco = ArcoCirculoFabrica::arcoTresPontos($primeiro, $pontoArco, $ultimo);
        $areaArco = $arco->area();
        $poligonoNovo = PolilinhaFabrica::criarPolilinhaPontos($pontosIntersecao, true, fechado: true);
        if (count($poligonoNovo) < 3) {
            return $areaArco;
        }
        $propriedade = PropriedadePoligono::executar($poligonoNovo);

        return $areaArco + $propriedade->area;
    }

    public static function primeiroCruzarCirculo(array $pontos, Circulo $circulo): Ponto
    {
        foreach ($pontos as $ponto) {
            if ($circulo->pontoFronteiraCirculo($ponto)) {
                return $ponto;
            }
        }
    }

    public static function ultimoCruzarCirculo(array $pontos, Circulo $circulo): Ponto
    {
        $pontos = array_reverse($pontos);
        foreach ($pontos as $ponto) {
            if ($circulo->pontoFronteiraCirculo($ponto)) {
                return $ponto;
            }
        }
    }
}
