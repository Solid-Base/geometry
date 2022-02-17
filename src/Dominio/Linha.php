<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Dominio;

use InvalidArgumentException;
use JsonSerializable;
use Solidbase\Geometria\Aplicacao\Modificadores\Transformacao;
use Solidbase\Geometria\Dominio\Fabrica\VetorFabrica;
use Solidbase\Geometria\Dominio\Trait\TransformacaoTrait;

/**
 * @property-read Ponto     $origem
 * @property-read Ponto     $final
 * @property-read Vetor     $direcao
 * @property-read float|int $comprimento
 */
class Linha implements JsonSerializable, TransformacaoInterface
{
    use TransformacaoTrait;
    private float|int $comprimento;

    public function __construct(
        private Ponto $origem,
        private Vetor $direcao,
        float|int $comprimento
    ) {
        $this->direcao = $direcao->vetorUnitario();
        $this->comprimento = $comprimento;
    }

    public function __get($name)
    {
        return match ($name) {
            'origem' => $this->origem,
            'final' => $this->pontoFinal(),
            'direcao' => $this->direcao,
            'comprimento' => $this->comprimento,
            default => throw new InvalidArgumentException('Prorpriedade solicitada não existe')
        };
    }

    public function aplicarTransformacao(Transformacao $transformacao): static
    {
        $origem = $transformacao->dePonto($this->origem);
        $direcao = $transformacao->deVetor($this->direcao);
        $this->origem = $origem;
        $this->direcao = $direcao;
        $this->comprimento *= $transformacao->obtenhaEscala();

        return $this;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'origem' => $this->origem,
            'direcao' => $this->direcao,
            'comprimento' => $this->comprimento,
        ];
    }

    public function eParelo(self $linha): bool
    {
        return $linha->direcao->temMesmaDirecao($this->direcao);
    }

    public function eCoplanar(self $linha): bool
    {
        $origem = $this->origem->eIgual($linha->origem) ? $linha->origem : $linha->final;
        $vetorPonto = VetorFabrica::apartirDoisPonto($this->origem, $origem);
        $produtoMisto = $this->direcao->produtoMisto($linha->direcao, $vetorPonto);

        return eZero($produtoMisto);
    }

    public function distanciaPontoLinha(Ponto $ponto): float
    {
        $vetorAuxiliar = VetorFabrica::apartirDoisPonto($this->origem, $ponto);
        $vetorial = $vetorAuxiliar->produtoVetorial($this->direcao);

        return $vetorial->modulo();
    }

    public function pontoPertenceLinha(Ponto $ponto): bool
    {
        if ($ponto->eIgual($this->origem) || $ponto->eIgual($this->final)) {
            return true;
        }
        $vetor = VetorFabrica::apartirDoisPonto($ponto, $this->origem)->vetorUnitario();
        if (!$vetor->temMesmaDirecao($this->direcao)) {
            return false;
        }

        return true;
    }

    public function pontoPertenceSegmento(Ponto $ponto): bool
    {
        if (!$this->pontoPertenceLinha($ponto)) {
            return false;
        }
        $distOrigem = $ponto->distanciaParaPonto($this->origem);
        $distFinal = $ponto->distanciaParaPonto($this->final);

        return eMenor($distOrigem, $this->comprimento) && eMenor($distFinal, $this->comprimento);
    }

    public function pontoRetaComprimento(float|int $comprimento): Ponto
    {
        $origem = $this->origem;
        $diretor = $this->direcao;

        return $origem->somar($diretor->escalar($comprimento));
    }

    protected function pontoFinal(): Ponto
    {
        return $this->pontoRetaComprimento($this->comprimento);
    }
}
