<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Poligono;

use Solidbase\Geometria\Dominio\Circulo;
use Solidbase\Geometria\Dominio\Fabrica\LinhaFabrica;
use Solidbase\Geometria\Dominio\Polilinha;

class CirculoInterceptaPoligono
{
    public function __construct(private Polilinha $poligono)
    {
    }

    public function executar(Circulo $circulo): bool
    {
        $centro = $circulo->centro;
        $pontos = $this->poligono->pontos();
        $quantidade = \count($pontos);
        for ($i = 1; $i < $quantidade; ++$i) {
            $p1 = $pontos[$i - 1];
            $p2 = $pontos[$i];
            $linha = LinhaFabrica::apartirDoisPonto($p1, $p2);
            if ($linha->distanciaPontoLinha($centro) < $circulo->raio) {
                return true;
            }
        }

        return false;
    }
}
