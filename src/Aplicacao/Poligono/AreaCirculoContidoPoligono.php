<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Poligono;

use Solidbase\Geometria\Aplicacao\Interseccao\InterseccaoLinhaCirculo;
use Solidbase\Geometria\Dominio\Arco;
use Solidbase\Geometria\Dominio\Circulo;
use Solidbase\Geometria\Dominio\Fabrica\ArcoCirculoFabrica;
use Solidbase\Geometria\Dominio\Fabrica\LinhaFabrica;
use Solidbase\Geometria\Dominio\Linha;
use Solidbase\Geometria\Dominio\Polilinha;

class AreaCirculoContidoPoligono
{
    private PontoPertencePoligono $pontoPertente;

    public function __construct(private Polilinha $poligono)
    {
        $this->pontoPertente = new PontoPertencePoligono($poligono);
    }

    public function executar(Circulo $circulo): bool
    {
        $pontos = $this->poligono->pontos();
        /**
         * @var array<Linha,Linha>[]
         */
        $segmentos = [];
        $pontoPertence = $this->centroPertenceAoCirculo($circulo);
        $centroCirculo = $circulo;
        $quantidade = \count($pontos);
        for ($i = 1; $i < $quantidade; ++$i) {
            $p1 = $pontos[$i - 1];
            $p2 = $pontos[$i];
            $linha = LinhaFabrica::apartirDoisPonto($p1, $p2);
            $intersecaoCirculo = new InterseccaoLinhaCirculo($linha, $circulo);
            if (!$intersecaoCirculo->possuiInterseccao()) {
                continue;
            }
            $linhaIntersecao = $intersecaoCirculo->executar();
            if (null === $linhaIntersecao) {
                continue;
            }
            $segmentos[] = [$linha, $linhaIntersecao];
        }
        $quantidadeSegmentos = \count($segmentos);
        for ($i = 0; $i < $quantidadeSegmentos; ++$i) {
            [$linhaPoligono ,$linhaIntersecao] = $segmentos[$i];
            if ($linhaPoligono->pontoPertenceSegmento($linhaIntersecao->origem)
             && $linhaPoligono->pontoPertenceSegmento($linhaIntersecao->final)) {
                $arco = $this->gerarArco($linhaIntersecao, $circulo, $pontoPertence);
            }
        }

        return false;
    }

    private function centroPertenceAoCirculo(Circulo $circulo): bool
    {
        $pontoPertence = new PontoPertencePoligono($this->poligono);

        return $pontoPertence->executar($circulo->centro);
    }

    private function gerarArco(Linha $linha, Circulo $circulo, bool $centroPertence): Arco
    {
        $pontoMedio = $linha->pontoRetaComprimento($linha->comprimento / 2);
        $linhaCirculo = LinhaFabrica::apartirDoisPonto($circulo->centro, $pontoMedio);
        $ponto3 = $centroPertence ? $linhaCirculo->pontoRetaComprimento($circulo->raio) : $linhaCirculo->pontoRetaComprimento(-$circulo->raio);

        return ArcoCirculoFabrica::arcoTresPontos($linha->origem, $linha->final, $ponto3);
    }
}
