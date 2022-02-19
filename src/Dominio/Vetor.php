<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Dominio;

use Exception;
use Solidbase\Geometria\Aplicacao\Modificadores\Transformacao;

final class Vetor extends Ponto
{
    public function aplicarTransformacao(Transformacao $transformacao): static
    {
        $novo = $transformacao->deVetor($this);
        $this->x = $novo->x;
        $this->y = $novo->y;
        $this->z = $novo->z;

        return $this;
    }

    public function temMesmaDirecao(self $vetor): bool
    {
        if ($vetor->eNulo() || $this->eNulo()) {
            return false;
        }
        if ($this->vetorUnitario()->eIgual($vetor->vetorUnitario())) {
            return true;
        }
        if ($this->vetorUnitario()->escalar(-1)->eIgual($vetor->vetorUnitario())) {
            return true;
        }

        return false;
    }

    public function temMesmoSentido(self $vetor): bool
    {
        return $this->vetorUnitario()->subtrair($vetor->vetorUnitario())->eNulo();
    }

    public function produtoInterno(self $vetor): float
    {
        $x = ($vetor->x * $this->x);
        $y = ($vetor->y * $this->y);
        $z = ($vetor->z * $this->z);

        return $x + $y + $z;
    }

    public function modulo(): float
    {
        $x = $this->x ** 2;
        $y = ($this->y ** 2);
        $z = ($this->z ** 2);
        $modulo = sqrt($x + $y + $z);

        return eZero($modulo) ? 0 : (eInteiro($modulo) ? round($modulo, 0) : $modulo);
    }

    public function escalar(float|int $fator): static
    {
        $x = ($this->x * $fator);
        $y = ($this->y * $fator);
        $z = ($this->z * $fator);

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
        $angulo = ($this->produtoInterno($vetor) / ($this->modulo() * $vetor->modulo()));
        $retorno = acos($angulo);

        return normalizar($retorno);
    }

    public function anguloAbsoluto(): float
    {
        $angulo = atan2($this->y, $this->x);
        $angulo += eMenor($angulo, 0) ? 2 * M_PI : 0;

        return normalizar($angulo);
    }

    public function produtoVetorial(self $vetor): static
    {
        $x = (($this->y * $vetor->z) - ($this->z * $vetor->y));
        $y = (($this->z * $vetor->x) - ($this->x * $vetor->z));
        $z = (($this->x * $vetor->y) - ($this->y * $vetor->x));

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
        $escalar = $this->produtoInterno($vetorU) / $modulo ** 2;

        return $this->escalar($escalar);
    }

    public function eNulo(): bool
    {
        $modulo = $this->modulo();

        return eZero($modulo);
    }
}
