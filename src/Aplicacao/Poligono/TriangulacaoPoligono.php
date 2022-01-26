<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Poligono;

use Solidbase\Geometria\Aplicacao\Interseccao\InterseccaoLinhas;
use Solidbase\Geometria\Dominio\Fabrica\LinhaFabrica;
use Solidbase\Geometria\Dominio\Fabrica\PolilinhaFabrica;
use Solidbase\Geometria\Dominio\Fabrica\VetorFabrica;
use Solidbase\Geometria\Dominio\Polilinha;
use Solidbase\Geometria\Dominio\Ponto;

class TriangulacaoPoligono
{
    private array $triangulos = [];

    public function __construct(private Polilinha $poligono)
    {
        $poligono->fecharPolilinha();
    }

    public function triangular(Polilinha $poligono): void
    {
        $quantidade = \count($poligono);
        if ($quantidade <= 3) {
            return;
        }
        $pontos = $poligono->pontos();
        for ($i = 2; $i < $quantidade; ++$i) {
            $p1 = $pontos[$i - 2];
            $p2 = $pontos[$i - 1];
            $p3 = $pontos[$i];
            // $orientacao = $this->orientacao2D($p1, $p2, $p3);
            $angulo = $this->anguloPontos($p1, $p2, $p3);
            if ($angulo > M_PI) {
                continue;
            }
            if ($this->segmentoIntercepta($p1, $p3, $poligono, $i)) {
                continue;
            }
            unset($pontos[$i - 1]);
            $this->triangulos[] = [$p1, $p2, $p3];

            $poligono = PolilinhaFabrica::criarPolilinhaPontos($pontos);

            $this->triangular($poligono);

            return;
        }
    }

    private function segmentoIntercepta(Ponto $p1, Ponto $p3, Polilinha $poligono, int $key): bool
    {
        $linha = LinhaFabrica::apartirDoisPonto($p1, $p3);
        $pontos = $poligono->pontos();
        $quantidade = \count($poligono);
        for ($i = $key; $i < $quantidade; ++$i) {
            $pl1 = $pontos[$i - 1];
            $pl2 = $pontos[$i];
            $linhaL = LinhaFabrica::apartirDoisPonto($pl1, $pl2);
            if ($linhaL->eParelo($linha)) {
                continue;
            }

            $pontoIntersecao = InterseccaoLinhas::executar($linha, $linhaL);
            if ($pontoIntersecao->eIgual($p1) || $pontoIntersecao->eIgual($p3)) {
                continue;
            }
            if ($linha->pontoPertenceSegmento($pontoIntersecao) && $linhaL->pontoPertenceSegmento($pontoIntersecao)) {
                return true;
            }
        }

        return false;
    }

    private function anguloPontos(Ponto $p1, Ponto $p2, Ponto $p3): float
    {
        $vetor1 = VetorFabrica::apartirDoisPonto($p1, $p2);
        $vetor2 = VetorFabrica::apartirDoisPonto($p2, $p3);

        return $vetor1->angulo($vetor2);
    }

    private function orientacao2D(Ponto $p1, Ponto $p2, Ponto $p3): int
    {
        $vetor1 = VetorFabrica::apartirDoisPonto($p1, $p2);
        $vetor2 = VetorFabrica::apartirDoisPonto($p2, $p3);
        $valor = $vetor1->produtoVetorial($vetor2);

        if ($valor->z < 0) {
            return -1;
        }
        if ($valor->z > 0) {
            return 1;
        }

        return 0;
    }
}
