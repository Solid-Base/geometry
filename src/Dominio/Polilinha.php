<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Dominio;

use Countable;
use DomainException;
use JsonSerializable;
use Solidbase\Geometria\Aplicacao\Modificadores\Transformacao;
use Solidbase\Geometria\Colecao\ColecaoPontos;
use Solidbase\Geometria\Dominio\Trait\TransformacaoTrait;

class Polilinha implements Countable, JsonSerializable, TransformacaoInterface
{
    use TransformacaoTrait;

    private ColecaoPontos $pontos;

    public function __construct(private bool $fechado = false)
    {
        $this->pontos = new ColecaoPontos();
    }

    public function __serialize(): array
    {
        $pontos = $this->pontos->map(fn (Ponto $p) => serialize($p));

        return ['pontos' => $pontos];
    }

    public function __unserialize(array $data): void
    {
        $pontos = $data['pontos'];
        $pontos = array_map(fn (string $p) => unserialize($p), $pontos);
        $this->pontos = ColecaoPontos::deArray($pontos);
    }

    public function aplicarTransformacao(Transformacao $transformacao): static
    {
        $this->pontos->each(fn (Ponto $p) => $p->aplicarTransformacao($transformacao));

        return $this;
    }

    public function jsonSerialize(): mixed
    {
        return $this->pontos;
    }

    public function count(): int
    {
        $total = count($this->pontos);
        $total += $this->fechado && !$this->ultimoEIgualPrimeiro() ? 1 : 0;

        return $total;
    }

    public function adicionarPonto(Ponto $ponto): self
    {
        if (Ponto::class === $ponto::class || Vetor::class === $ponto::class) {
            $x = $ponto->x;
            $y = $ponto->y;
            $z = $ponto->z;
            $ponto = new PontoPoligono($x, $y, $z);
        }
        $this->pontos[] = $ponto;

        return $this;
    }

    public function fecharPolilinha(): self
    {
        if (\count($this->pontos) <= 2) {
            throw new DomainException('Para fechar uma polilinha, é necessário pelo menos 3 pontos');
        }
        $this->fechado = true;

        return $this;
        // if ($this->ePoligono()) {
        //     return $this;
        // }
        // $primeiro = reset($this->pontos);
        // $this->pontos[] = $primeiro;

        // return $this;
    }

    public function ePoligono(): bool
    {
        return $this->fechado;
    }

    public function pontos(): ColecaoPontos
    {
        $retorno = clone $this->pontos;
        $ultimo = $retorno->primeiro(null);
        if ($this->fechado && !$this->ultimoEIgualPrimeiro()) {
            $retorno->adicionar($ultimo);
        }

        return $retorno;
    }

    private function ultimoEIgualPrimeiro(): bool
    {
        $ultimo = $this->pontos->ultimo(null);
        $primeiro = $this->pontos->primeiro(null);

        return $ultimo->eIgual($primeiro);
    }
}
