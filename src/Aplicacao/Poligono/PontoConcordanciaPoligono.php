<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Poligono;

use Solidbase\Geometria\Aplicacao\Interseccao\InterseccaoLinhas;
use Solidbase\Geometria\Aplicacao\Modificadores\Transformacao;
use Solidbase\Geometria\Dominio\Fabrica\LinhaFabrica;
use Solidbase\Geometria\Dominio\Fabrica\VetorFabrica;
use Solidbase\Geometria\Dominio\PontoPoligono;

class PontoConcordanciaPoligono
{
    public static function executar(PontoPoligono $p1, PontoPoligono $p2): array
    {
        $angulo = atan($p1->concordancia) * 4;
        $rotacao = ($angulo / 2);
        $transformacao = Transformacao::criarRotacaoPonto(VetorFabrica::BaseZ(), -$rotacao, $p1);
        $p2Novo = $transformacao->dePonto($p2);
        $transformacao = Transformacao::criarRotacaoPonto(VetorFabrica::BaseZ(), $rotacao, $p2);
        $p1Novo = $transformacao->dePonto($p1);

        $linha1 = LinhaFabrica::apartirDoisPonto($p1, $p2Novo);
        $linha2 = LinhaFabrica::apartirDoisPonto($p2, $p1Novo);
        $pontoIntersecao = InterseccaoLinhas::executar($linha1, $linha2);
        $distancia = $p1->distanciaParaPonto($pontoIntersecao);
        $raio = abs($distancia / tan($rotacao));

        return [$pontoIntersecao, $raio];
    }
}
