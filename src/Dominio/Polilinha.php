<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Dominio;

use Countable;
use DomainException;
use JsonSerializable;
use Solidbase\Geometria\Aplicacao\Modificadores\Transformacao;
use Solidbase\Geometria\Dominio\Fabrica\VetorFabrica;

class Polilinha implements PrecisaoInterface, Countable, JsonSerializable
{
    /**
     * @var PontoPoligono[]
     */
    private array $pontos;

    public function __construct()
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

    public function jsonSerialize(): mixed
    {
        return $this->pontos;
    }

    public function count(): int
    {
        return \count($this->pontos);
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
        if ($this->ePoligono()) {
            return $this;
        }
        $primeiro = reset($this->pontos);
        $this->pontos[] = $primeiro;

        return $this;
    }

    public function ePoligono(): bool
    {
        $primeiro = reset($this->pontos);
        $ultimo = end($this->pontos);

        return $primeiro->distanciaParaPonto($ultimo) <= self::PRECISAO;
    }

    /**
     * @return PontoPoligono[]
     */
    public function pontos(): array
    {
        return $this->pontos;
    }

    public function mover(float $dx = 0, float $dy = 0, float $dz = 0): void
    {
        $transformacao = Transformacao::criarTranslacao(new Ponto($dx, $dy, $dz));
        $pontos = array_map(fn (Ponto $p) => $transformacao->dePonto($p), $this->pontos);
        $this->pontos = $pontos;
    }

    public function rotacionar(float $angulo, ?Ponto $ponto = null): void
    {
        if (eZero($angulo)) {
            return;
        }
        if (null === $ponto) {
            $transformacao = Transformacao::criarRotacao(VetorFabrica::BaseZ(), $angulo);
        } else {
            $transformacao = Transformacao::criarRotacaoPonto(VetorFabrica::BaseZ(), $angulo, $ponto);
        }
        $pontos = array_map(fn (Ponto $p) => $transformacao->dePonto($p), $this->pontos);
        $this->pontos = $pontos;
    }
}
