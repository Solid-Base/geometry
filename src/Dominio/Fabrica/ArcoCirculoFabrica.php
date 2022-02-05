<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Dominio\Fabrica;

use DomainException;
use Solidbase\Geometria\Aplicacao\Interseccao\InterseccaoLinhas;
use Solidbase\Geometria\Aplicacao\Pontos\PontosAlinhados;
use Solidbase\Geometria\Aplicacao\Pontos\RotacaoPontoEnum;
use Solidbase\Geometria\Aplicacao\Pontos\SentidoRotacaoTresPontos;
use Solidbase\Geometria\Dominio\Arco;
use Solidbase\Geometria\Dominio\Circulo;
use Solidbase\Geometria\Dominio\Linha;
use Solidbase\Geometria\Dominio\Ponto;

class ArcoCirculoFabrica
{
    public static function arcoTresPontos(Ponto $ponto1, Ponto $ponto2, Ponto $ponto3): Arco
    {
        $centro = self::centroTresPonto($ponto1, $ponto2, $ponto3);
        $raio = $centro->distanciaParaPonto($ponto1);

        $vetorP1 = VetorFabrica::apartirDoisPonto($centro, $ponto1);
        $anguloP1 = $vetorP1->anguloAbsoluto();
        $vetorP3 = VetorFabrica::apartirDoisPonto($centro, $ponto3);
        $anguloP3 = $vetorP3->anguloAbsoluto();

        $rotacao = SentidoRotacaoTresPontos::executar($ponto1, $ponto2, $ponto3);

        $anguloInicial = RotacaoPontoEnum::ANTI_HORARIO == $rotacao ? $anguloP1 : $anguloP3;
        $anguloFinal = RotacaoPontoEnum::ANTI_HORARIO == $rotacao ? $anguloP3 : $anguloP1;

        return new Arco($centro, $raio, $anguloInicial, $anguloFinal);
    }

    public static function arcoCentroInicioFim(Ponto $centro, Ponto $inicio, Ponto $fim): Arco
    {
        $vetorP1 = VetorFabrica::apartirDoisPonto($centro, $inicio);
        $anguloP1 = $vetorP1->anguloAbsoluto();
        $vetorP2 = VetorFabrica::apartirDoisPonto($centro, $fim);
        $anguloP2 = $vetorP2->anguloAbsoluto();
        $raio = $centro->distanciaParaPonto($inicio);

        return new Arco($centro, $raio, $anguloP1, $anguloP2);
    }

    public static function circuloTresPontos(Ponto $ponto1, Ponto $ponto2, Ponto $ponto3): Circulo
    {
        $centro = self::centroTresPonto($ponto1, $ponto2, $ponto3);
        $raio = $centro->distanciaParaPonto($ponto1);

        return new Circulo($centro, $raio);
    }

    private static function InterseccaoLinha(Linha $linha1, Linha $linha2): Ponto
    {
        return InterseccaoLinhas::executar($linha1, $linha2);
    }

    private static function centroTresPonto(Ponto $ponto1, Ponto $ponto2, Ponto $ponto3): Ponto
    {
        if (PontosAlinhados::executar($ponto1, $ponto2, $ponto3)) {
            throw new DomainException('Para fazer um arco ou circulo apartir de três pontos, é necessario que os mesmos não seja alinhado.');
        }
        $v1 = VetorFabrica::apartirDoisPonto($ponto1, $ponto2)->produtoVetorial(VetorFabrica::BaseZ());
        $v2 = VetorFabrica::apartirDoisPonto($ponto1, $ponto3)->produtoVetorial(VetorFabrica::BaseZ());
        $pontoMedioP1P2 = $ponto1->pontoMedio($ponto2);
        $pontoMedioP1P3 = $ponto1->pontoMedio($ponto3);
        $retaP1P2 = new Linha($pontoMedioP1P2, $v1, 1);
        $retaP1P3 = new Linha($pontoMedioP1P3, $v2, 1);

        return self::InterseccaoLinha($retaP1P2, $retaP1P3);
    }
}
