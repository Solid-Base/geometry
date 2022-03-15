<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Dominio;

use InvalidArgumentException;
use JsonSerializable;
use Solidbase\Geometria\Aplicacao\Modificadores\Transformacao;
use Solidbase\Geometria\Dominio\Enum\QuadranteEnum;
use Solidbase\Geometria\Dominio\Trait\TransformacaoTrait;

/**
 * @property-read float $x
 * @property-read float $y
 * @property-read float $z
 */
class Ponto implements JsonSerializable, TransformacaoInterface
{
    use TransformacaoTrait;
    protected float $x;
    protected float $y;
    protected float $z;

    public function __construct(
        float $x = 0,
        float $y = 0,
        float $z = 0
    ) {
        $this->x = normalizar($x);
        $this->y = normalizar($y);
        $this->z = normalizar($z);
    }

    public function __serialize(): array
    {
        return [
            'x' => $this->x,
            'y' => $this->y,
            'z' => $this->z,
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->x = $data['x'];
        $this->y = $data['y'];
        $this->z = $data['z'];
    }

    public function __get($name): float
    {
        return match ($name) {
            'x' => $this->x,
            'y' => $this->y,
            'z' => $this->z,
            default => throw new InvalidArgumentException('Prorpriedade solicitada não existe')
        };
    }

    public function aplicarTransformacao(Transformacao $transformacao): static
    {
        $novo = $transformacao->dePonto($this);
        $this->x = $novo->x;
        $this->y = $novo->y;
        $this->z = $novo->z;

        return $this;
    }

    public function jsonSerialize(): mixed
    {
        return $this->__serialize();
    }

    public function distanciaParaPonto(self $ponto): float
    {
        $x2 = ($ponto->x - $this->x) ** 2;
        $y2 = ($ponto->y - $this->y) ** 2;
        $z2 = ($ponto->z - $this->z) ** 2;

        return sqrt($x2 + $y2 + $z2);
    }

    public function somar(self $ponto): static
    {
        $x = ($this->x + $ponto->x);
        $y = ($this->y + $ponto->y);
        $z = ($this->z + $ponto->z);

        return new static($x, $y, $z);
    }

    public function subtrair(self $ponto): static
    {
        $x = ($this->x - $ponto->x);
        $y = ($this->y - $ponto->y);
        $z = ($this->z - $ponto->z);

        return new static($x, $y, $z);
    }

    public function pontoMedio(self $ponto): static
    {
        $x = ($this->x + $ponto->x) / 2;
        $y = ($this->y + $ponto->y) / 2;
        $z = ($this->z + $ponto->z) / 2;

        return new static($x, $y, $z);
    }

    public function eIgual(self $ponto): bool
    {
        $distancia = $this->distanciaParaPonto($ponto);

        return eZero($distancia);
    }

    public function toArray(): array
    {
        return $this->__serialize();
    }

    public function quadrante(): QuadranteEnum
    {
        $angulo = normatizarAngulo(atan2($this->y, $this->x));
        if (eZero($angulo) || entre(0, $angulo, M_PI / 2, false)) {
            return QuadranteEnum::PRIMEIRO;
        }
        if (entre(M_PI / 2, $angulo, M_PI, false) || eIgual($angulo, M_PI / 2)) {
            return QuadranteEnum::SEGUNDO;
        }
        if (entre(M_PI, $angulo, 3 * M_PI / 2, false) || eIgual($angulo, 3 * M_PI / 2)) {
            return QuadranteEnum::TERCEIRO;
        }

        return QuadranteEnum::QUARTO;
    }
}
