<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Offset;

use Solidbase\Geometria\Aplicacao\Interseccao\InterseccaoLinhas;
use Solidbase\Geometria\Dominio\Fabrica\VetorFabrica;
use Solidbase\Geometria\Dominio\Linha;
use Solidbase\Geometria\Dominio\Ponto;

class OffsetLinha
{
    public function __construct(private Linha $linha)
    {
    }

    public function executar(float $offset, Ponto $pontoRef): Linha
    {
        $perpendicular = VetorFabrica::Perpendicular($this->linha->direcao);
        $linhaPerpendicular = new Linha($pontoRef, $perpendicular, 1);
        $intersecao = new InterseccaoLinhas($this->linha, $linhaPerpendicular);
        $ponto = $intersecao->executar();
        $direcao = VetorFabrica::apartirDoisPonto($ponto, $pontoRef)->vetorUnitario();
        $linhaPerpendicular = new Linha($this->linha->origem, $direcao, $offset);

        $final = $linhaPerpendicular->final;

        return new Linha($final, $this->linha->direcao, $this->linha->comprimento);
    }
}
