<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Dominio;

use Solidbase\Geometria\Aplicacao\Modificadores\Transformacao;

interface TransformacaoInterface
{
    public function mover(float|int $dx, float|int $dy, float|int $dz = 0): static;

    public function rotacionar(float|int $angulo, ?Ponto $ponto = null): static;

    public function aplicarEscala(float|int $escala, ?Ponto $ponto = null): static;

    public function aplicarEspelho(Plano|Linha $planoOuLinha): static;

    public function aplicarTransformacao(Transformacao $transformacao): static;
}
