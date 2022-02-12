<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Dominio;

use DomainException;
use InvalidArgumentException;
use Solidbase\Geometria\Dominio\Fabrica\VetorFabrica;

/**
 * @property-read Vetor $u
 * @property-read Vetor $v
 * @property-read Vetor $normal
 * @property-read Ponto $origem
 */
class Plano implements PrecisaoInterface
{
    private Vetor $u;
    private Vetor $v;

    public function __construct(
        protected Ponto $origem,
        protected Vetor $normal
    ) {
        if ($normal->eNulo()) {
            throw new DomainException('Não é possível criar Planos com normal nula');
        }
        $this->normal = $normal->vetorUnitario();
        $this->u = VetorFabrica::Perpendicular($normal)->vetorUnitario();
        $this->v = $this->normal->produtoVetorial($this->u)->vetorUnitario();
    }

    public function __get($name)
    {
        return match ($name) {
            'u' => $this->u,
            'v' => $this->v,
            'normal' => $this->normal,
            'origem' => $this->origem,
            default => throw new InvalidArgumentException('Propriedade informada não existe!')
        };
    }

    public function distanciaPontoAoPlano(Ponto $ponto): float
    {
        $v = VetorFabrica::apartirDoisPonto($this->origem, $ponto);

        return $this->normal->produtoInterno($v);
    }

    public function pontoPertenceAoPlano(Ponto $ponto): bool
    {
        return eZero($this->distanciaPontoAoPlano($ponto));
    }

    public function projecaoPontoPlano(Ponto $ponto): Ponto
    {
        $distancia = $this->distanciaPontoAoPlano($ponto);

        return $ponto->subtrair($this->normal->escalar($distancia));
    }

    public function projecaoPontoPlanoZ(Ponto $ponto): Ponto
    {
        if (eZero($this->normal->z)) {
            throw new DomainException('Não é possível calcular a projeção do ponto no plano Z');
        }
        [$a,$b,$c,$d] = $this->equacaoPlano();
        $z = (-$d - $b * $ponto->y - $a * $ponto->x) / $c;

        return new Ponto($ponto->x, $ponto->y, $z);
        // $p = VetorFabrica::apartirPonto($this->origem);
        // $normal = $this->normal;
        // $comprimento = $normal->produtoInterno($p);
        // $z = ($comprimento - ($normal->x * $ponto->x + $normal->y * $ponto->y)) / $normal->z;

        // return new Ponto($ponto->x, $ponto->y, $z);
    }

    public function projecaoPontoPlanoX(Ponto $ponto): Ponto
    {
        if (eZero($this->normal->x)) {
            throw new DomainException('Não é possível calcular a projeção do ponto no plano X');
        }
        [$a,$b,$c,$d] = $this->equacaoPlano();
        $x = (-$d - $b * $ponto->y - $c * $ponto->z) / $a;

        return new Ponto($x, $ponto->y, $ponto->z);
        // $p = VetorFabrica::apartirPonto($this->origem);
        // $normal = $this->normal;
        // $comprimento = $normal->produtoInterno($p);
        // $x = ($comprimento - ($normal->y * $ponto->y + $normal->z * $ponto->z)) / $normal->x;
        // $pontoRetorno = clone $ponto;
        // $pontoRetorno->x = $x;

        // return $pontoRetorno;
    }

    public function projecaoPontoPlanoY(Ponto $ponto): Ponto
    {
        if (eZero($this->normal->y)) {
            throw new DomainException('Não é possível calcular a projeção do ponto no plano Y');
        }
        [$a,$b,$c,$d] = $this->equacaoPlano();
        $y = (-$d - $a * $ponto->x - $c * $ponto->z) / $b;

        return new Ponto($ponto->x, $y, $ponto->z);
        // $p = VetorFabrica::apartirPonto($this->origem);
        // $normal = $this->normal;
        // $comprimento = $normal->produtoInterno($p);
        // $y = ($comprimento - ($normal->x * $ponto->x + $normal->z * $ponto->z)) / $normal->y;
        // $pontoRetorno = clone $ponto;
        // $pontoRetorno->y = $y;

        // return $pontoRetorno;
    }

    /**
     * @return float[]
     */
    public function equacaoPlano(): array
    {
        $origem = $this->origem;
        $a = $this->normal->x;
        $b = $this->normal->y;
        $c = $this->normal->z;
        $d = ($a * $origem->x) + ($b * $origem->y) + ($c * $origem->z);

        return [$a, $b, $c, $d];
    }
}
