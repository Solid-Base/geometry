<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Interseccao;

use Solidbase\Geometria\Dominio\Circulo;
use Solidbase\Geometria\Dominio\Fabrica\LinhaFabrica;
use Solidbase\Geometria\Dominio\Fabrica\VetorFabrica;
use Solidbase\Geometria\Dominio\Linha;
use Solidbase\Geometria\Dominio\Ponto;
use SolidBase\Matematica\Aritimetica\Numero;

class InterseccaoLinhaCirculo
{
    private function __construct()
    {
    }

    public static function executar(Linha $linha, Circulo $circulo): ?array
    {
        if (!self::possuiInterseccao($linha, $circulo)) {
            return null;
        }
        $distancia = self::distanciaCentroLinha($linha, $circulo->centro);
        $comprimento = eZero(subtrair($circulo->raio, $distancia)) ?
                        numero(0, PRECISAO_SOLIDBASE) :
                        raiz(potencia($circulo->raio, 2)->subtrair(potencia($distancia, 2)));
        $pontoIntersecao = self::pontoIntersecao($linha, $circulo);
        $direcaoLinha = $linha->direcao;
        $ponto1 = $pontoIntersecao->somar($direcaoLinha->escalar($comprimento));
        if (eZero($comprimento)) {
            return [$ponto1];
        }
        $ponto2 = $pontoIntersecao->somar($direcaoLinha->escalar($comprimento->multiplicar(-1)));

        return [$ponto1, $ponto2];
    }

    public static function possuiInterseccao(Linha $linha, Circulo $circulo): bool
    {
        $distancia = $linha->distanciaPontoLinha($circulo->centro);
        $igual = eZero(subtrair($distancia, $circulo->raio));

        return eMenor($distancia, $circulo->raio) || $igual;
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

    private static function distanciaCentroLinha(Linha $linha, Ponto $ponto): Numero
    {
        return $linha->distanciaPontoLinha($ponto);
    }
}
