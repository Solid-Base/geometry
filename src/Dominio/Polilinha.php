<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Dominio;

use Countable;
use DomainException;
use JsonSerializable;
use Solidbase\Geometria\Aplicacao\Modificadores\Transformacao;
use Solidbase\Geometria\Dominio\Trait\TransformacaoTrait;

class Polilinha implements Countable, JsonSerializable, TransformacaoInterface
{
    use TransformacaoTrait;
    /**
     * @var PontoPoligono[]
     */
    private array $pontos;

    public function __construct(private bool $fechado = false)
    {
        $this->pontos = [];
    }

    public function __serialize(): array
    {
        $pontos = array_map(fn (Ponto $p) => serialize($p), $this->pontos);

        return ['pontos' => $pontos];
    }

    public function __unserialize(array $data): void
    {
        $pontos = $data['pontos'];
        $this->pontos = array_map(fn (string $p) => unserialize($p), $pontos);
    }

    public function aplicarTransformacao(Transformacao $transformacao): static
    {
        $pontos = array_map(fn (Ponto $p) => $transformacao->dePonto($p), $this->pontos);
        $this->pontos = $pontos;

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

    /**
     * @return PontoPoligono[]
     */
    public function pontos(): array
    {
        $retorno = array_values($this->pontos);
        $ultimo = end($this->pontos);
        if ($this->fechado && !$this->ultimoEIgualPrimeiro()) {
            $retorno[] = $retorno[0];
        }

        return $retorno;
    }

    private function ultimoEIgualPrimeiro(): bool
    {
        $ultimo = end($this->pontos);

        return $ultimo->eIgual($this->pontos[0]);
    }
}
