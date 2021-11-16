<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Interseccao;

use Solidbase\Geometria\Dominio\Circulo;
use Solidbase\Geometria\Dominio\Fabrica\LinhaFabrica;
use Solidbase\Geometria\Dominio\Fabrica\VetorFabrica;
use Solidbase\Geometria\Dominio\Linha;
use Solidbase\Geometria\Dominio\Ponto;

class InterseccaoLinhaCirculo
{
    public function __construct(private Linha $linha, private Circulo $circulo)
    {
    }

    public function possuiInterseccao(): bool
    {
        return $this->distanciaCentroLinha() <= $this->circulo->raio;
    }

    public function executar(): ?Linha
    {
        if (!$this->possuiInterseccao()) {
            return null;
        }
        $distancia = $this->distanciaCentroLinha();
        $comprimento = sqrt($this->circulo->raio ** 2 - $distancia ** 2);
        $pontoIntersecao = $this->pontoIntersecao();
        $direcaoLinha = $this->linha->direcao;
        $ponto1 = $pontoIntersecao->somar($direcaoLinha->escalar($comprimento));
        $ponto2 = $pontoIntersecao->somar($direcaoLinha->escalar(-$comprimento));

        return LinhaFabrica::apartirDoisPonto($ponto1, $ponto2);
    }

    private function pontoIntersecao(): Ponto
    {
        $perpendicular = VetorFabrica::Perpendicular($this->linha->direcao);
        $linhaPerpendicular = new Linha($this->circulo->centro, $perpendicular, 1);
        $intersecao = new InterseccaoLinhas($this->linha, $linhaPerpendicular);

        return $intersecao->executar();
    }

    private function distanciaCentroLinha(): float
    {
        return $this->linha->distanciaPontoLinha($this->circulo->centro);
    }
}
