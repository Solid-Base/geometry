<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Dominio;

use Exception;
use SolidBase\Matematica\Aritimetica\Numero;

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
        if ($this->vetorUnitario()->escalar(-1)->eIgual($vetor->vetorUnitario())) {
            return true;
        }

        return false;
    }

    public function temMesmoSentido(self $vetor): bool
    {
        return $this->vetorUnitario()->subtrair($vetor->vetorUnitario())->eNulo();
    }

    public function produtoInterno(self $vetor): Numero
    {
        $x = multiplicar($vetor->x, $this->x);
        $y = multiplicar($vetor->y, $this->y);
        $z = multiplicar($vetor->z, $this->z);

        return somar(somar($x, $y), $z);
    }

    public function modulo(): Numero
    {
        $x = potencia($this->x, 2);
        $y = potencia($this->y, 2);
        $z = potencia($this->z, 2);

        return somar(somar($x, $y), $z)->raiz();
    }

    public function escalar(float|Numero $fator): static
    {
        $x = multiplicar($this->x, $fator);
        $y = multiplicar($this->y, $fator);
        $z = multiplicar($this->z, $fator);

        return new static($x, $y, $z);
    }

    public function vetorUnitario(): static
    {
        if ($this->eNulo()) {
            throw new Exception('Vetores nulos não possui vetor unitário');
        }
        $modulo = $this->modulo();

        return $this->escalar(dividir(1, $modulo));
    }

    public function angulo(self $vetor): Numero
    {
        $angulo = dividir($this->produtoInterno($vetor), multiplicar($this->modulo(), $vetor->modulo()));

        return arcoCosseno($angulo);
    }

    public function anguloAbsoluto(): Numero
    {
        $unitario = $this->vetorUnitario();
        $angulo = arcoCosseno($unitario->x);
        if ($this->quadrante() > 2) {
            $angulo = subtrair(multiplicar(S_PI, 2), $angulo);
        }

        return $angulo;
    }

    public function produtoVetorial(self $vetor): static
    {
        $x = subtrair(multiplicar($this->y, $vetor->z), multiplicar($this->z, $vetor->y));
        $y = subtrair(multiplicar($this->z, $vetor->x), multiplicar($this->x, $vetor->z));
        $z = subtrair(multiplicar($this->x, $vetor->y), multiplicar($this->y, $vetor->x));

        return new static($x, $y, $z);
    }

    public function produtoMisto(self $vetorV, self $vetorW): Numero
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
        $escalar = dividir($this->produtoInterno($vetorU), potencia($modulo, 2));

        return $this->escalar($escalar);
    }

    public function eNulo(): bool
    {
        $modulo = $this->modulo();

        return eZero($modulo);
    }
}
