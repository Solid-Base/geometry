<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Interseccao;

use DomainException;
use Solidbase\Geometria\Dominio\Fabrica\VetorFabrica;
use Solidbase\Geometria\Dominio\Linha;
use Solidbase\Geometria\Dominio\Ponto;
use Solidbase\Geometria\Dominio\PrecisaoInterface;

class InterseccaoLinhas
{
    public function __construct(private Linha $linha1, private Linha $linha2)
    {
    }

    public function executar(): Ponto
    {
        if ($this->linha1->eParelo($this->linha2)) {
            throw new DomainException('Retas parelas não se interseptam');
        }
        if (!$this->linha1->eCoplanar($this->linha2)) {
            throw new DomainException('Somente retas coplanares possuem intersecção');
        }
        if ($this->linha1->origem->eIgual($this->linha2->origem)) {
            return $this->linha1->origem;
        }
        if ($this->linha1->final->eIgual($this->linha2->final)) {
            return $this->linha1->final;
        }
        [$s,] = $this->calcularTS();

        return $this->linha1->origem->somar($this->linha1->direcao->escalar($s));
    }

    private function calcularTS(): array
    {
        $k = $this->linha1->origem;
        $l = $this->linha1->pontoRetaComprimento(1);
        $m = $this->linha2->origem;
        $n = $this->linha2->pontoRetaComprimento(1);

        $diretorS = VetorFabrica::apartirDoisPonto($k, $l);
        $diretorR = VetorFabrica::apartirDoisPonto($n, $m);

        $determinante = $diretorR->produtoVetorial($diretorS);

        $diretorMk = VetorFabrica::apartirDoisPonto($k, $m);
        $vetorialRMk = $diretorR->produtoVetorial($diretorMk);
        $vetorialSMk = $diretorS->produtoVetorial($diretorMk);
        if ((!$this->retaPertenceOx($this->linha1) || !$this->retaPertenceOx($this->linha2))
        && (!$this->retaPertenceOy($this->linha1) || !$this->retaPertenceOy($this->linha2))) {
            $s = $vetorialRMk->z / $determinante->z;
            $t = $vetorialSMk->z / $determinante->z;

            return [$s, $t];
        }
        if ((!$this->retaPertenceOx($this->linha1) || !$this->retaPertenceOx($this->linha2))
        && (!$this->retaPertenceOz($this->linha1) || !$this->retaPertenceOz($this->linha2))) {
            $s = $vetorialRMk->y / $determinante->y;
            $t = $vetorialSMk->y / $determinante->y;

            return [$s, $t];
        }
        $s = $vetorialRMk->x / $determinante->x;
        $t = $vetorialSMk->x / $determinante->x;

        return [$s, $t];
    }

    private function retaPertenceOx(Linha $linha): bool
    {
        $direcao = $linha->direcao->vetorUnitario();

        return $this->eZero(abs($direcao->x) - 1);
    }

    private function retaPertenceOy(Linha $linha): bool
    {
        $direcao = $linha->direcao->vetorUnitario();

        return $this->eZero(abs($direcao->y) - 1);
    }

    private function retaPertenceOz(Linha $linha): bool
    {
        $direcao = $linha->direcao->vetorUnitario();

        return $this->eZero(abs($direcao->z) - 1);
    }

    private function eZero(float $numero): bool
    {
        return abs($numero) <= PrecisaoInterface::PRECISAO;
    }
}
