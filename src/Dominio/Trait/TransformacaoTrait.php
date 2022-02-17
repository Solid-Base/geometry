<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Dominio\Trait;

use Solidbase\Geometria\Aplicacao\Modificadores\Transformacao;
use Solidbase\Geometria\Dominio\Fabrica\VetorFabrica;
use Solidbase\Geometria\Dominio\Linha;
use Solidbase\Geometria\Dominio\Plano;
use Solidbase\Geometria\Dominio\Ponto;

trait TransformacaoTrait
{
    public function mover(float|int $dx, float|int $dy, float|int $dz = 0): static
    {
        $transformacao = Transformacao::criarTranslacao(new Ponto($dx, $dy, $dz));

        return $this->aplicarTransformacao($transformacao);
    }

    public function rotacionar(float|int $angulo, ?Ponto $ponto = null): static
    {
        if (eZero($angulo)) {
            return $this;
        }
        if (null === $ponto) {
            $transformacao = Transformacao::criarRotacao(VetorFabrica::BaseZ(), $angulo);
        } else {
            $transformacao = Transformacao::criarRotacaoPonto(VetorFabrica::BaseZ(), $angulo, $ponto);
        }

        return $this->aplicarTransformacao($transformacao);
    }

    public function aplicarEscala(float|int $escala, ?Ponto $ponto = null): static
    {
        if (null == $ponto || $ponto->eIgual(new Ponto())) {
            $transformacao = Transformacao::criarTranslacao(new Ponto());
            $transformacao->escalar($escala);

            return $this->aplicarTransformacao($transformacao);
        }
        $transformacao = Transformacao::criarEscalaPonto($escala, $ponto);

        return $this->aplicarTransformacao($transformacao);
    }

    public function aplicarEspelho(Plano|Linha $planoOuLinha): static
    {
        if ($planoOuLinha instanceof Linha) {
            $direcao = $planoOuLinha->direcao;
            $normal = $direcao->produtoVetorial(VetorFabrica::BaseZ());
            $planoOuLinha = new Plano($planoOuLinha->origem, $normal);
        }
        $transformacao = Transformacao::criarReflexao($planoOuLinha);

        return $this->aplicarTransformacao($transformacao);
    }
}
