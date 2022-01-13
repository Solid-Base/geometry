<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Interseccao;

use DomainException;
use Exception;
use Solidbase\Geometria\Dominio\Fabrica\LinhaFabrica;
use Solidbase\Geometria\Dominio\Linha;
use Solidbase\Geometria\Dominio\Polilinha;
use Solidbase\Geometria\Dominio\Ponto;

class IntersecaoLinhaPolilinha
{
    public function __construct(private Linha $linha, private Polilinha $polilinha)
    {
    }

    /**
     * @throws Exception
     * @throws DomainException
     *
     * @return null|Ponto[]
     */
    public function executar(): ?array
    {
        $pontos = $this->polilinha->pontos();
        $pontos[] = reset($pontos);
        $numeroPonto = \count($pontos);
        $pontosRetorno = [];
        for ($i = 1; $i < $numeroPonto; ++$i) {
            $p1 = $pontos[$i - 1];
            $p2 = $pontos[$i];
            $linha = LinhaFabrica::apartirDoisPonto($p1, $p2);
            if ($linha->eParelo($this->linha)) {
                continue;
            }
            $intersecao = new InterseccaoLinhas($this->linha, $linha);
            $ponto = $intersecao->executar();
            if (false !== array_search($ponto, $pontosRetorno, false)) {
                continue;
            }
            if ($linha->pontoPertenceSegmento($ponto)) {
                $pontosRetorno[] = $ponto;
            }
        }

        return \count($pontosRetorno) > 0 ? $pontosRetorno : null;
    }
}
