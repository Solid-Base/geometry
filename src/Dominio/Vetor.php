<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Dominio;

use Exception;

final class Vetor extends Ponto
{
    public function temMesmaDirecao(self $vetor): bool
    {
        if ($vetor->eNulo() || $this->eNulo()) {
            return false;
        }
        if ($this->vetorUnitario()->eIgual($vetor->vetorUnitario())) {
            return true;
        }
        $angulo = $this->angulo($vetor);

        return (abs($angulo) <= $this::PRECISAO) || abs(abs($angulo) - M_PI) <= $this::PRECISAO;
    }

    public function temMesmoSentido(self $vetor): bool
    {
        if (!$this->temMesmaDirecao($vetor)) {
            return false;
        }
        $angulo = $this->angulo($vetor);

        return abs($angulo) <= $this::PRECISAO;
    }

    public function produtoInterno(self $vetor): float
    {
        return $vetor->x * $this->x + $vetor->y * $this->y + $vetor->z * $this->z;
    }

    public function modulo(): float
    {
        return sqrt($this->x ** 2 + $this->y ** 2 + $this->z ** 2);
    }

    public function escalar(float $fator): self
    {
        $x = $this->x * $fator;
        $y = $this->y * $fator;
        $z = $this->z * $fator;

        return new self($x, $y, $z);
    }

    public function vetorUnitario(): self
    {
        if ($this->eNulo()) {
            throw new Exception('Vetores nulos não possui vetor unitário');
        }
        $modulo = $this->modulo();

        return $this->escalar(1 / $modulo);
    }

    public function angulo(self $vetor): float
    {
        $angulo = $this->produtoInterno($vetor) / ($this->modulo() * $vetor->modulo());

        return acos($angulo);
    }

    public function anguloAbsoluto(): float
    {
        $unitario = $this->vetorUnitario();
        $angulo = acos($unitario->x);
        if ($this->quadrante() > 2) {
            $angulo = 2 * M_PI - $angulo;
        }

        return $angulo;
    }

    public function produtoVetorial(self $vetor): self
    {
        $x = $this->y * $vetor->z - $this->z * $vetor->y;
        $y = $this->z * $vetor->x - $this->x * $vetor->z;
        $z = $this->x * $vetor->y - $this->y * $vetor->x;

        return new self($x, $y, $z);
    }

    public function produtoMisto(self $vetorV, self $vetorW): float
    {
        return ($this->produtoVetorial($vetorV))->produtoInterno($vetorW);
    }

    public function projecao(self $vetorU): self
    {
        if ($this->eNulo()) {
            throw new Exception('Não é possível encontrar projeção em vetores nulos');
        }
        if ($vetorU->eNulo()) {
            return new self();
        }
        $modulo = $this->modulo();
        $escalar = $this->produtoInterno($vetorU) / ($modulo ** 2);

        return $this->escalar($escalar);
    }

    public function eNulo(): bool
    {
        $modulo = $this->modulo();
        if ($modulo <= $this::PRECISAO) {
            return true;
        }

        return false;
    }
}
