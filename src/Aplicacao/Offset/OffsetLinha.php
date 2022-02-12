<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Offset;

use Solidbase\Geometria\Aplicacao\Offset\Enum\DirecaoOffsetReta;
use Solidbase\Geometria\Dominio\Fabrica\LinhaFabrica;
use Solidbase\Geometria\Dominio\Fabrica\VetorFabrica;
use Solidbase\Geometria\Dominio\Linha;

class OffsetLinha
{
    private function __construct()
    {
    }

    public static function executar(float|int $offset, Linha $linha, DirecaoOffsetReta $direcao): Linha
    {
        $perpendicular = VetorFabrica::Perpendicular($linha->direcao)->vetorUnitario();
        $origem = $linha->origem->somar($perpendicular->escalar($offset * $direcao->value));
        $final = $linha->final->somar($perpendicular->escalar($offset * $direcao->value));

        return LinhaFabrica::apartirDoisPonto($origem, $final);
    }
}
