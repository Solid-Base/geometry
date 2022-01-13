<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Offset;

use Solidbase\Geometria\Aplicacao\Interseccao\InterseccaoLinhas;
use Solidbase\Geometria\Dominio\Fabrica\LinhaFabrica;
use Solidbase\Geometria\Dominio\Fabrica\PolilinhaFabrica;
use Solidbase\Geometria\Dominio\Polilinha;
use Solidbase\Geometria\Dominio\Ponto;

class OffsetPoligono
{
    public function __construct(private Polilinha $polilinha)
    {
    }

    public function executar(float $offset, Ponto $pontoRef): Polilinha
    {
        $numeroPonto = \count($this->polilinha);
        $pontos = $this->polilinha->pontos();
        $linhas = [];
        for ($i = 1; $i < $numeroPonto; ++$i) {
            $p1 = $pontos[$i - 1];
            $p2 = $pontos[$i];
            $linha = LinhaFabrica::apartirDoisPonto($p1, $p2);
            $offsetLinha = new OffsetLinha($linha);
            $linha = $offsetLinha->executar($offset, $pontoRef);
            $linhas[] = $linha;
        }
        $numeroLinha = \count($linhas);
        $pontos = [];
        for ($i = 1; $i < $numeroLinha; ++$i) {
            $linha1 = $linhas[$i - 1];
            $linha2 = $linhas[$i];
            $intersecao = new InterseccaoLinhas($linha1, $linha2);
            $ponto = $intersecao->executar();
            $pontos[] = $ponto;
        }
        if ($this->polilinha->ePoligono()) {
            $linha1 = reset($linhas);
            $linha2 = end($linhas);
            $intersecao = new InterseccaoLinhas($linha1, $linha2);
            $ponto = $intersecao->executar();
            $pontos[] = $ponto;
        }

        $poligono = PolilinhaFabrica::criarPolilinhaPontos($pontos);
        if ($this->polilinha->ePoligono()) {
            $poligono->fecharPolilinha();
        }

        return $poligono;
    }
}
