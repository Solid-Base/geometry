<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Poligono;

use Solidbase\Geometria\Dominio\Ponto;
use SolidBase\Matematica\Aritimetica\Numero;

class DadosPoligono
{
    public function __construct(
        public readonly Numero $area,
        public readonly int $sentido,
        public readonly TipoPoligonoEnum $tipo,
        public readonly Ponto $centro,
        public readonly Numero $segundoMomentoInerciaX,
        public readonly Numero $segundoMomentoInerciaY,
        public readonly Numero $momentoInerciaPrincipalX,
        public readonly Numero $momentoInerciaPrincipalY,
    ) {
    }
}
