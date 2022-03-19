<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Dominio;

use InvalidArgumentException;
use Solidbase\Geometria\Aplicacao\Modificadores\Transformacao;
use Solidbase\Geometria\Dominio\Fabrica\ArcoCirculoFabrica;
use Solidbase\Geometria\Dominio\Fabrica\VetorFabrica;
use Solidbase\Geometria\Dominio\Trait\TransformacaoTrait;

/**
 * @property-read Ponto     $centro
 * @property-read float|int $raio
 * @property-read float|int $anguloInicial
 * @property-read float|int $anguloFinal
 * @property-read float|int $anguloTotal
 * @property-read float|int $area
 * @property-read float|int $comprimento
 */
class Arco implements TransformacaoInterface
{
    use TransformacaoTrait;
    private float $raio;
    private float $anguloInicial;
    private float $anguloFinal;

    public function __construct(private Ponto $centro, float|int $raio, float|int $anguloInicial, float|int $anguloFinal)
    {
        if (eMenor($raio, 0)) {
            throw new InvalidArgumentException('O raio do arco deve ser um numero positivo maior que zero');
        }
        $this->raio = $raio;
        $this->anguloInicial = $anguloInicial;
        $this->anguloFinal = $anguloFinal;
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

    public function __clone()
    {
        $this->centro = clone $this->centro;
    }

    public function aplicarTransformacao(Transformacao $transformacao): static
    {
        $pontoInicial = $transformacao->dePonto($this->pontoInicial());
        $pontoFinal = $transformacao->dePonto($this->pontoFinal());
        $centro = $transformacao->dePonto($this->centro);
        $arco = ArcoCirculoFabrica::arcoCentroInicioFim($centro, $pontoInicial, $pontoFinal);
        $this->centro = $arco->centro;
        $this->anguloInicial = $arco->anguloInicial;
        $this->anguloFinal = $arco->anguloFinal;
        $this->raio = normalizar($arco->raio);
        unset($arco);

        return $this;
    }

    public function anguloTotal(): float
    {
        $total = $this->anguloFinal - $this->anguloInicial;
        if (eMenor($total, 0)) {
            $total += M_PI * 2;
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

        return $this->raio ** 2 * ($anguloTotal - sin($anguloTotal)) / 2;
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

        return eZero(($arco->anguloInicial - $this->anguloInicial)) && eZero(($arco->anguloFinal - $this->anguloFinal));
    }
}
