<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Dominio;

use DomainException;
use InvalidArgumentException;
use Solidbase\Geometria\Aplicacao\Modificadores\Transformacao;
use Solidbase\Geometria\Dominio\Fabrica\VetorFabrica;
use Solidbase\Geometria\Dominio\Trait\TransformacaoTrait;

/**
 * @property-read Ponto $centro
 * @property-read float $raioMaior
 * @property-read float $raioMenor
 * @property-read Vetor $direcao
 */
class Elipse implements TransformacaoInterface
{
    use TransformacaoTrait;
    private Vetor $direcao;

    public function __construct(private Ponto $centro, private float $raioMaior, private float $raioMenor, float $angulo = 0)
    {
        if ($raioMaior <= 0 || $this->raioMenor <= 0) {
            throw new InvalidArgumentException('Os raios da elipse deve ser um numero positivo maior que zero');
        }
        if ($raioMenor >= $raioMaior) {
            throw new DomainException('O raio menor nõa deve ser maior que o raio maior');
        }
        $this->direcao = VetorFabrica::DirecaoModulo($angulo);
    }

    public function __get($name)
    {
        return match ($name) {
            'centro' => $this->centro,
            'raioMaior' => $this->raioMaior,
            'raioMenor' => $this->raioMenor,
            'direcao' => $this->direcao,
            default => throw new InvalidArgumentException('A propriedade solicitada não existe')
        };
    }

    public function area(): float
    {
        return M_PI * $this->raioMaior * $this->raioMenor;
    }

    public function perimetro(): float
    {
        $c = sqrt($this->raioMaior ** 2 + $this->raioMenor ** 2);
        $e = $c / $this->raioMaior;

        return $this->raioMaior * M_PI * (2 - ($e ** 2) / 2 + (3 * $e ** 4) / 16);
    }

    public function aplicarTransformacao(Transformacao $transformacao): static
    {
        $this->centro = $transformacao->dePonto($this->centro);
        $this->direcao = $transformacao->deVetor($this->direcao);

        $this->raioMaior *= $transformacao->obtenhaEscala();
        $this->raioMenor *= $transformacao->obtenhaEscala();

        return $this;
    }
}
