<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Dominio;

use InvalidArgumentException;
use Solidbase\Geometria\Aplicacao\Modificadores\Transformacao;
use Solidbase\Geometria\Dominio\Fabrica\ArcoCirculoFabrica;
use Solidbase\Geometria\Dominio\Fabrica\VetorFabrica;

/**
 * @property-read Ponto $centro
 * @property-read float $raio
 * @property-read float $anguloInicial
 * @property-read float $anguloFinal
 * @property-read float $anguloTotal
 * @property-read float $area
 * @property-read float $comprimento
 */
class Arco
{
    public function __construct(private Ponto $centro, private float $raio, private float $anguloInicial, private float $anguloFinal)
    {
        if ($raio < 0) {
            throw new InvalidArgumentException('O raio do arco deve ser um numero positivo maior que zero');
        }
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

    public function anguloTotal(): float
    {
        $total = $this->anguloFinal - $this->anguloInicial;
        if ($total < 0) {
            $total += 2 * M_PI;
        }

        return $total;
    }

    public function comprimentoArco(): float
    {
        return $this->anguloTotal() * $this->raio;
    }

    public function area(): float
    {
        $anguloTotal = $this->anguloTotal();

        return ($this->raio ** 2) * ($anguloTotal - sin($anguloTotal)) / 2;
    }

    public function pontoInicial(): Ponto
    {
        $transformaca = Transformacao::criarRotacaoPonto(VetorFabrica::BaseZ(), $this->anguloInicial, $this->centro);
        $ponto = $this->centro->somar(VetorFabrica::BaseX()->escalar($this->raio));

        return $transformaca->dePonto($ponto);
    }

    public function pontoFinal(): Ponto
    {
        $transformaca = Transformacao::criarRotacaoPonto(VetorFabrica::BaseZ(), $this->anguloFinal, $this->centro);
        $ponto = $this->centro->somar(VetorFabrica::BaseX()->escalar($this->raio));

        return $transformaca->dePonto($ponto);
    }

    public function pontoPertenceArco(Ponto $ponto): bool
    {
        if (!eZero($this->centro->distanciaParaPonto($ponto) - $this->raio)) {
            return false;
        }
        $pInicial = $this->pontoInicial();
        $pFinal = $this->pontoFinal();
        if (eZero($pFinal->distanciaParaPonto($ponto)) || eZero($pInicial->distanciaParaPonto($ponto))) {
            return true;
        }
        $arco = ArcoCirculoFabrica::arcoTresPontos($pInicial, $ponto, $pFinal);

        return eZero($arco->anguloInicial - $this->anguloInicial) && eZero($arco->anguloFinal - $this->anguloFinal);
    }
}
