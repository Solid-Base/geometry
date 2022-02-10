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
        $angulo = arcoTangente($p1->concordancia)->multiplicar(4);
        $rotacao = dividir($angulo, 2);
        $transformacao = Transformacao::criarRotacaoPonto(VetorFabrica::BaseZ(), multiplicar($rotacao, -1), $p1);
        $p2Novo = $transformacao->dePonto($p2);
        $transformacao = Transformacao::criarRotacaoPonto(VetorFabrica::BaseZ(), $rotacao, $p2);
        $p1Novo = $transformacao->dePonto($p1);

        $linha1 = LinhaFabrica::apartirDoisPonto($p1, $p2Novo);
        $linha2 = LinhaFabrica::apartirDoisPonto($p2, $p1Novo);
        $pontoIntersecao = InterseccaoLinhas::executar($linha1, $linha2);
        $distancia = $p1->distanciaParaPonto($pontoIntersecao);
        $raio = dividir($distancia, tangente($rotacao))->arredondar(PRECISAO_SOLIDBASE)->modulo();

        return [$pontoIntersecao, $raio->valor()];
    }
}
