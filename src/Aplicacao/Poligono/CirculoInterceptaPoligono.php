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

    public function executar(Circulo $circulo): bool
    {
        $pontos = $this->poligono->pontos();
        $quantidade = \count($pontos);
        for ($i = 1; $i < $quantidade; ++$i) {
            $p1 = $pontos[$i - 1];
            $p2 = $pontos[$i];
            $linha = LinhaFabrica::apartirDoisPonto($p1, $p2);
            if (!InterseccaoLinhaCirculo::possuiInterseccao($linha, $circulo)) {
                continue;
            }
            $linhaIntersecao = InterseccaoLinhaCirculo::executar($linha, $circulo);
            if ($linha->pontoPertenceSegmento($linhaIntersecao->origem) || $linha->pontoPertenceSegmento($linhaIntersecao->final)) {
                return true;
            }
        }

        return false;
    }
}
