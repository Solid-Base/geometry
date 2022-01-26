<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Interseccao;

use Solidbase\Geometria\Dominio\Circulo;
use Solidbase\Geometria\Dominio\Fabrica\LinhaFabrica;
use Solidbase\Geometria\Dominio\Fabrica\VetorFabrica;
use Solidbase\Geometria\Dominio\Linha;
use Solidbase\Geometria\Dominio\Ponto;

class InterseccaoLinhaCirculo
{
    public function __construct(private Linha $linha, private Circulo $circulo)
    {
    }

    public static function executar(Linha $linha, Circulo $circulo): ?Linha
    {
        if (!self::possuiInterseccao($linha, $circulo)) {
            return null;
        }
        $distancia = self::distanciaCentroLinha($linha, $circulo->centro);
        $comprimento = sqrt($circulo->raio ** 2 - $distancia ** 2);
        $pontoIntersecao = self::pontoIntersecao($linha, $circulo);
        $direcaoLinha = $linha->direcao;
        $ponto1 = $pontoIntersecao->somar($direcaoLinha->escalar($comprimento));
        $ponto2 = $pontoIntersecao->somar($direcaoLinha->escalar(-$comprimento));

        return LinhaFabrica::apartirDoisPonto($ponto1, $ponto2);
    }

    public static function possuiInterseccao(Linha $linha, Circulo $circulo): bool
    {
        $distancia = self::distanciaCentroLinha($linha, $circulo->centro);

        return eMenor(self::distanciaCentroLinha($linha, $circulo->centro), $circulo->raio);
    }

    // public function executarOld(): ?Linha
    // {
    //     if (!$this->possuiInterseccao()) {
    //         return null;
    //     }
    //     $distancia = $this->distanciaCentroLinha();
    //     $comprimento = sqrt($this->circulo->raio ** 2 - $distancia ** 2);
    //     $pontoIntersecao = $this->pontoIntersecao();
    //     $direcaoLinha = $this->linha->direcao;
    //     $ponto1 = $pontoIntersecao->somar($direcaoLinha->escalar($comprimento));
    //     $ponto2 = $pontoIntersecao->somar($direcaoLinha->escalar(-$comprimento));

    //     return LinhaFabrica::apartirDoisPonto($ponto1, $ponto2);
    // }

    private static function pontoIntersecao(Linha $linha, Circulo $circulo): Ponto
    {
        $perpendicular = VetorFabrica::Perpendicular($linha->direcao);
        $linhaPerpendicular = new Linha($circulo->centro, $perpendicular, 1);

        return InterseccaoLinhas::executar($linha, $linhaPerpendicular);
    }

    private static function distanciaCentroLinha(Linha $linha, Ponto $ponto): float
    {
        return $linha->distanciaPontoLinha($ponto);
    }
}
