<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Interseccao;

use DomainException;
use Exception;
use Solidbase\Geometria\Dominio\Fabrica\LinhaFabrica;
use Solidbase\Geometria\Dominio\Fabrica\PolilinhaFabrica;
use Solidbase\Geometria\Dominio\Linha;
use Solidbase\Geometria\Dominio\Polilinha;
use Solidbase\Geometria\Dominio\Ponto;

class IntersecaoLinhaPolilinha
{
    private Polilinha $poligonoNovo;

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
        $this->polilinha = clone $this->polilinha;
        $this->polilinha->fecharPolilinha();
        $pontos = $this->polilinha->pontos();
        $numeroPonto = \count($pontos);
        $pontosRetorno = [];
        $pontosPoligono = [];
        for ($i = 1; $i < $numeroPonto; ++$i) {
            $p1 = $pontos[$i - 1];
            $p2 = $pontos[$i];
            $linha = LinhaFabrica::apartirDoisPonto($p1, $p2);
            if ($linha->eParelo($this->linha)) {
                $pontosPoligono[] = $p2;

                continue;
            }
            $intersecao = new InterseccaoLinhas($this->linha, $linha);
            $ponto = $intersecao->executar();
            if (false !== array_search($ponto, $pontosRetorno, false)) {
                $pontosPoligono[] = $p2;

                continue;
            }
            if ($linha->pontoPertenceSegmento($ponto)) {
                $pontosPoligono[] = $ponto;
                $pontosPoligono[] = $p2;
                $pontosRetorno[] = $ponto;

                continue;
            }

            $pontosPoligono[] = $p2;
        }
        $this->poligonoNovo = PolilinhaFabrica::criarPolilinhaPontos($pontosPoligono);

        return \count($pontosRetorno) > 0 ? $pontosRetorno : null;
    }

    public function poligonoPontosIntersecao(): Polilinha
    {
        if (empty($this->poligonoNovo)) {
            $this->executar();
        }

        return $this->poligonoNovo;
    }

    public static function Pontos(Linha $linha, Polilinha $poligono): ?array
    {
        [$pontos,] = self::executarIntersecao($linha, $poligono);
        if (0 == count($pontos)) {
            return null;
        }

        return $pontos;
    }

    public static function Poligono(Linha $linha, Polilinha $poligono): Polilinha
    {
        [,$poligono] = self::executarIntersecao($linha, $poligono);

        return $poligono;
    }

    private static function executarIntersecao(Linha $linhaIntersecao, Polilinha $poligono): array
    {
        $polilinha = clone $poligono;
        $polilinha->fecharPolilinha();
        $pontos = $polilinha->pontos();
        $numeroPonto = \count($pontos);
        $pontosRetorno = [];
        $pontosPoligono = [];
        for ($i = 1; $i < $numeroPonto; ++$i) {
            $p1 = $pontos[$i - 1];
            $p2 = $pontos[$i];
            $linha = LinhaFabrica::apartirDoisPonto($p1, $p2);
            if ($linha->eParelo($linhaIntersecao)) {
                $pontosPoligono[] = $p2;

                continue;
            }
            $intersecao = new InterseccaoLinhas($linhaIntersecao, $linha);
            $ponto = $intersecao->executar();
            if (false !== array_search($ponto, $pontosRetorno, false)) {
                $pontosPoligono[] = $p2;

                continue;
            }
            if ($linha->pontoPertenceSegmento($ponto)) {
                $pontosPoligono[] = $ponto;
                $pontosPoligono[] = $p2;
                $pontosRetorno[] = $ponto;

                continue;
            }

            $pontosPoligono[] = $p2;
        }
        $poligonoNovo = PolilinhaFabrica::criarPolilinhaPontos($pontosPoligono);

        return [$pontosPoligono, $poligonoNovo];
    }
}
