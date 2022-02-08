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
        $angulo = numero($this->angulo($vetor));
        $pi = numero(S_PI);

        return eZero($angulo) || eZero(subtrair($angulo->modulo(), $pi));
    }

    public function temMesmoSentido(self $vetor): bool
    {
        if (!$this->temMesmaDirecao($vetor)) {
            return false;
        }
        $angulo = numero($this->angulo($vetor));

        return eZero($angulo);
    }

    public function produtoInterno(self $vetor): float
    {
        $x = multiplicar($vetor->x, $this->x);
        $y = multiplicar($vetor->y, $this->y);
        $z = multiplicar($vetor->z, $this->z);
        $soma = somar(somar($x, $y), $z);

        return $soma->valor();
    }

    public function modulo(): float
    {
        $x = potencia($this->x, 2);
        $y = potencia($this->y, 2);
        $z = potencia($this->z, 2);
        $modulo = somar(somar($x, $y), $z)->raiz();

        return $modulo->valor();
    }

    public function escalar(float $fator): static
    {
        $x = $this->x * $fator;
        $y = $this->y * $fator;
        $z = $this->z * $fator;

        return new static($x, $y, $z);
    }

    public function vetorUnitario(): static
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
        $valor = round($angulo, 6);

        return acos((float) $valor);
    }

    public function anguloAbsoluto(): float
    {
        $unitario = $this->vetorUnitario();
        $angulo = acos(arredondar($unitario->x, 5));
        if ($this->quadrante() > 2) {
            $angulo = 2 * M_PI - $angulo;
        }

        return $angulo;
    }

    public function produtoVetorial(self $vetor): static
    {
        $x = $this->y * $vetor->z - $this->z * $vetor->y;
        $y = $this->z * $vetor->x - $this->x * $vetor->z;
        $z = $this->x * $vetor->y - $this->y * $vetor->x;

        return new static($x, $y, $z);
    }

    public function produtoMisto(self $vetorV, self $vetorW): float
    {
        return ($this->produtoVetorial($vetorV))->produtoInterno($vetorW);
    }

    public function projecao(self $vetorU): static
    {
        if ($this->eNulo()) {
            throw new Exception('Não é possível encontrar projeção em vetores nulos');
        }
        if ($vetorU->eNulo()) {
            return new static();
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
