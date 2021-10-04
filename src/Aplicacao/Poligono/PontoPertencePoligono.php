<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Poligono;

use Solidbase\Geometria\Aplicacao\Interseccao\InterseccaoLinhas;
use Solidbase\Geometria\Dominio\Fabrica\LinhaFabrica;
use Solidbase\Geometria\Dominio\Fabrica\VetorFabrica;
use Solidbase\Geometria\Dominio\Linha;
use Solidbase\Geometria\Dominio\Polilinha;
use Solidbase\Geometria\Dominio\Ponto;
use Solidbase\Geometria\Dominio\PrecisaoInterface;

class PontoPertencePoligono
{
    public function __construct(private Polilinha $poligono, private Ponto $ponto)
    {
        $poligono->fecharPolilinha();
    }

    public function executar(): bool
    {
        $linhaComparacao = new Linha($this->ponto, VetorFabrica::BaseX(), 1);
        $numeroPontos = \count($this->poligono);
        $contagem = 0;
        $pontos = $this->poligono->pontos();
        for ($i = 0; $i < $numeroPontos - 1; ++$i) {
            $p1 = $pontos[$i];
            $p2 = $pontos[$i + 1];
            $linha = LinhaFabrica::apartirDoisPonto($p1, $p2);
            if ($linha->pontoPertenceLinha($this->ponto)) {
                continue;
            }
            if ($linha->eParelo($linhaComparacao)) {
                continue;
            }
            $intersecao = new InterseccaoLinhas($linha, $linhaComparacao);
            $pontoIntersecao = $intersecao->executar();
            if ($pontoIntersecao->x < $this->ponto->x) {
                continue;
            }
            if ($this->eZero($pontoIntersecao->distanciaParaPonto($p1))
            || $this->eZero($pontoIntersecao->distanciaParaPonto($p2))) {
                if (($this->eZero($pontoIntersecao->y - $p1->y)) && $pontoIntersecao->y > $p2->y) {
                    ++$contagem;
                }
                if (($this->eZero($pontoIntersecao->y - $p2->y)) && $pontoIntersecao->y > $p1->y) {
                    ++$contagem;
                }

                continue;
            }
            $distanciaP1P2 = $p1->distanciaParaPonto($p2);
            if ($pontoIntersecao->distanciaParaPonto($p1) > $distanciaP1P2
            || $pontoIntersecao->distanciaParaPonto($p2) > $distanciaP1P2) {
                continue;
            }
            ++$contagem;
        }

        return 1 === $contagem % 2;
    }

    private function eZero(float $numero): bool
    {
        return abs($numero) <= PrecisaoInterface::PRECISAO;
    }
}
