<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Dominio;

use Countable;
use DomainException;

class Polilinha implements PrecisaoInterface, Countable
{
    /**
     * @var PontoPoligono[]
     */
    private array $pontos;
    

    public function __construct()
    {
        $this->pontos = [];
    }

    public function count()
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
}
