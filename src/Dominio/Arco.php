<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Dominio;

use InvalidArgumentException;
use Solidbase\Geometria\Aplicacao\Modificadores\Transformacao;
use Solidbase\Geometria\Dominio\Fabrica\ArcoCirculoFabrica;
use Solidbase\Geometria\Dominio\Fabrica\VetorFabrica;
use SolidBase\Matematica\Aritimetica\Numero;

/**
 * @property-read Ponto  $centro
 * @property-read Numero $raio
 * @property-read Numero $anguloInicial
 * @property-read Numero $anguloFinal
 * @property-read Numero $anguloTotal
 * @property-read Numero $area
 * @property-read Numero $comprimento
 */
class Arco
{
    private Numero $raio;
    private Numero $anguloInicial;
    private Numero $anguloFinal;

    public function __construct(private Ponto $centro, float|Numero $raio, float|Numero $anguloInicial, float|Numero $anguloFinal)
    {
        if (eMenor($raio, 0)) {
            throw new InvalidArgumentException('O raio do arco deve ser um numero positivo maior que zero');
        }
        $this->raio = numero($raio, PRECISAO_SOLIDBASE);
        $this->anguloInicial = numero($anguloInicial, PRECISAO_SOLIDBASE);
        $this->anguloFinal = numero($anguloFinal, PRECISAO_SOLIDBASE);
    }

    public function __get($name)
    {
        return match ($name) {
            'centro' => $this->centro,
            'raio' => $this->raio,
            'anguloInicial' => $this->anguloInicial,
            'anguloFinal' => $this->anguloFinal,
            'anguloTotal' => $this->anguloTotal(),
            'comprimento' => $this->comprimentoArco(),
            'area' => $this->area(),
            default => throw new InvalidArgumentException('A propriedade solicitada não existe')
        };
    }

    public function anguloTotal(): Numero
    {
        $total = subtrair($this->anguloFinal, $this->anguloInicial);
        if (eMenor($total, 0)) {
            $total->somar(multiplicar(S_PI, 2));
        }

        return $total;
    }

    public function comprimentoArco(): Numero
    {
        return multiplicar($this->anguloTotal(), $this->raio);
    }

    public function area(): Numero
    {
        $anguloTotal = $this->anguloTotal();

        return dividir(multiplicar(potencia($this->raio, 2), subtrair($anguloTotal, seno($anguloTotal))), 2);
    }

    public function pontoInicial(): Ponto
    {
        $transformaca = Transformacao::criarRotacaoPonto(VetorFabrica::BaseZ(), $this->anguloInicial->valor(), $this->centro);
        $ponto = $this->centro->somar(VetorFabrica::BaseX()->escalar($this->raio));

        return $transformaca->dePonto($ponto);
    }

    public function pontoFinal(): Ponto
    {
        $transformaca = Transformacao::criarRotacaoPonto(VetorFabrica::BaseZ(), $this->anguloFinal->valor(), $this->centro);
        $ponto = $this->centro->somar(VetorFabrica::BaseX()->escalar($this->raio));

        return $transformaca->dePonto($ponto);
    }

    public function pontoPertenceArco(Ponto $ponto): bool
    {
        if (!eZero(subtrair($this->centro->distanciaParaPonto($ponto), $this->raio))) {
            return false;
        }
        $pInicial = $this->pontoInicial();
        $pFinal = $this->pontoFinal();
        if (eZero($pFinal->distanciaParaPonto($ponto)) || eZero($pInicial->distanciaParaPonto($ponto))) {
            return true;
        }
        $arco = ArcoCirculoFabrica::arcoTresPontos($pInicial, $ponto, $pFinal);

        return eZero(subtrair($arco->anguloInicial, $this->anguloInicial)) && eZero(subtrair($arco->anguloFinal, $this->anguloFinal));
    }
}
