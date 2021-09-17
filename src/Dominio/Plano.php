<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Dominio;

use DomainException;
use Solidbase\Geometria\Dominio\Fabrica\VetorFabrica;

class Plano
{
    public const PRECISAO = 1E-10;

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
        $this->v = $this->u->produtoVetorial($this->normal)->vetorUnitario();
    }

    public function distanciaPontoAoPlano(Ponto $ponto): float
    {
        $v = VetorFabrica::apartirDoisPonto($this->origem, $ponto);

        return $this->normal->produtoInterno($v);
    }

    public function pontoPertenceAoPlano(Ponto $ponto): bool
    {
        return abs($this->distanciaPontoAoPlano($ponto)) <= $this::PRECISAO;
    }
}
