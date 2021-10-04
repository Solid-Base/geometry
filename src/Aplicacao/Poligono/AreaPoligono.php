<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Poligono;

use DomainException;
use Solidbase\Geometria\Dominio\Polilinha;

class AreaPoligono
{
    
    public function __construct(private Polilinha $poligono)
    {
        $poligono->fecharPolilinha();
    }

    public function executar(): float
    {
        $soma = 0;
        if (count($this->poligono) < 3) {
            throw new DomainException('É necessário ter pelo menos três pontos');
        }
        $pontos = $this->poligono->pontos();
        foreach ($pontos as $i => $ponto) {
            if (!isset($pontos[$i + 1])) {
                break;
            }
            $proximo = $pontos[$i + 1];
            $soma += ($ponto->x + $proximo->x) * ($proximo->y - $ponto->y);
        }

        return abs($soma) / 2;
    }
}
