<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Poligono;

use Solidbase\Geometria\Dominio\Ponto;

class DadosPoligono
{
    public function __construct(
        public readonly float $area,
        public readonly int $sentido,
        public readonly Ponto $centro,
        public readonly float $segundoMomentoInerciaX,
        public readonly float $segundoMomentoInerciaY,
        public readonly float $momentoInerciaPrincipalX,
        public readonly float $momentoInerciaPrincipalY,
    ) {
    }
}
