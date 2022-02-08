<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Poligono;

use Solidbase\Geometria\Aplicacao\Interseccao\InterseccaoLinhaCirculo;
use Solidbase\Geometria\Dominio\Circulo;
use Solidbase\Geometria\Dominio\Fabrica\LinhaFabrica;
use Solidbase\Geometria\Dominio\Polilinha;

class CirculoInterceptaPoligono
{
    public function __construct(private Polilinha $poligono)
    {
    }

    public static function executar(Polilinha $poligono, Circulo $circulo): bool
    {
        $pontos = $poligono->pontos();
        $quantidade = \count($pontos);
        for ($i = 1; $i < $quantidade; ++$i) {
            $p1 = $pontos[$i - 1];
            $p2 = $pontos[$i];
            $linha = LinhaFabrica::apartirDoisPonto($p1, $p2);
            if (!InterseccaoLinhaCirculo::possuiInterseccao($linha, $circulo)) {
                continue;
            }
            $linhaIntersecao = InterseccaoLinhaCirculo::executar($linha, $circulo);
            if (null === $linhaIntersecao) {
                return false;
            }
            $origem = $linhaIntersecao[0];
            $final = $linhaIntersecao[1] ?? $origem;
            if ($linha->pontoPertenceSegmento($origem) || $linha->pontoPertenceSegmento($final)) {
                return true;
            }
        }

        return false;
    }
}
