<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Poligono;

use Solidbase\Geometria\Aplicacao\Interseccao\InterseccaoLinhaCirculo;
use Solidbase\Geometria\Dominio\Circulo;
use Solidbase\Geometria\Dominio\Fabrica\LinhaFabrica;
use Solidbase\Geometria\Dominio\Polilinha;

class AreaCirculoContidoPoligono
{
    private PontoPertencePoligono $pontoPertente;

    public function __construct(private Polilinha $poligono)
    {
        $this->pontoPertente = new PontoPertencePoligono($poligono);
    }

    public function executar(Circulo $circulo): bool
    {
        $pontos = $this->poligono->pontos();
        $quantidade = \count($pontos);
        for ($i = 1; $i < $quantidade; ++$i) {
            $p1 = $pontos[$i - 1];
            $p2 = $pontos[$i];
            $linha = LinhaFabrica::apartirDoisPonto($p1, $p2);
            $intersecaoCirculo = new InterseccaoLinhaCirculo($linha, $circulo);
            if (!$intersecaoCirculo->possuiInterseccao()) {
                continue;
            }
            $linhaIntersecao = $intersecaoCirculo->executar();
            if (!$this->pontoPertente->executar($linhaIntersecao->origem)) {
                if ($linha->pontoPertenceSegmento($linhaIntersecao->origem) || $linha->pontoPertenceSegmento($linhaIntersecao->final)) {
                    return true;
                }
            }
        }

        return false;
    }
}
