<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Modificadores;

use Solidbase\Geometria\Aplicacao\Modificadores\Fabrica\FabricaMatrizTransformacao;
use Solidbase\Geometria\Dominio\Fabrica\VetorFabrica;
use Solidbase\Geometria\Dominio\Plano;
use Solidbase\Geometria\Dominio\Ponto;
use Solidbase\Geometria\Dominio\Vetor;
use SolidBase\Matematica\Algebra\FabricaMatriz;
use SolidBase\Matematica\Algebra\Matriz;
use SolidBase\Matematica\Algebra\MatrizInversa;

class Transformacao
{
    private bool $mirror = false;
    private Matriz $matriz;
    private Ponto $origem;
    private float $escala = 1;

    private function __construct(Matriz $matriz, Ponto $origem)
    {
        $this->matriz = $matriz;
        $this->origem = $origem;
    }

    public function __clone()
    {
        $this->matriz = clone $this->matriz;
        $this->origem = clone $this->origem;
    }

    public static function criarRotacao(Vetor $eixo, float $angulo): self
    {
        return new self(FabricaMatrizTransformacao::MatrizRotacao($eixo, $angulo), new Ponto());
    }

    public static function criarRotacaoPonto(Vetor $eixo, float|int $angulo, Ponto $origem): self
    {
        $matriz = FabricaMatrizTransformacao::MatrizRotacao($eixo, $angulo);
        $matrizRotacao = new self($matriz, new Ponto());
        if ($origem->eIgual(new Ponto())) {
            return $matrizRotacao;
        }
        $vetor = VetorFabrica::apartirPonto($origem);
        $translacao = $vetor->escalar(-1);
        $matrizTlinha = self::criarTranslacao($translacao);
        $matrizT = self::criarTranslacao($vetor);
        $primeira = $matrizTlinha->multiplicar($matrizRotacao);

        return $primeira->multiplicar($matrizT);
    }

    /**
     * Transformação de Householder
     * A=I-2NN^t
     * N = Vetor Normal
     * I = Matriz Identidade.
     */
    public static function criarReflexao(Plano $plano): self
    {
        $normal = $plano->normal;
        $origem = VetorFabrica::apartirPonto($plano->origem);
        $d = $origem->escalar(-1)->produtoInterno($normal);
        $x = -2 * $normal->x * $d;
        $y = -2 * $normal->y * $d;
        $z = -2 * $normal->z * $d;
        $origem = new Ponto($x, $y, $z);
        $matriz = FabricaMatrizTransformacao::Reflexao($plano);

        $retorno = new self($matriz, $origem);
        $retorno->mirror = true;

        return $retorno;
    }

    public static function criarTranslacao(Ponto $vetor): self
    {
        return new self(FabricaMatriz::Identidade(3), $vetor);
    }

    public function dePonto(Ponto $ponto): Ponto
    {
        $matriz = clone $this->matriz;
        $matriz->adicionarLinha([0, 0, 0]);
        $matriz->adicionarColuna([$this->origem->x, $this->origem->y, $this->origem->z, 1]);
        $pontoM = new Matriz([[$ponto->x], [$ponto->y], [$ponto->z], [1]]);
        $pontoT = $matriz->Multiplicar($pontoM)->obtenhaMatriz();
        $pontoR = new Vetor($pontoT[0][0], $pontoT[1][0], $pontoT[2][0]);
        $pontoR = $pontoR->escalar($this->escala);

        return new Ponto($pontoR->x, $pontoR->y, $pontoR->z);
    }

    public function deVetor(Vetor $vetor): Vetor
    {
        $matriz = clone $this->matriz;
        $pontoM = new Matriz([[$vetor->x], [$vetor->y], [$vetor->z]]);
        $pontoT = $matriz->Multiplicar($pontoM)->obtenhaMatriz();

        return new Vetor($pontoT[0][0], $pontoT[1][0], $pontoT[2][0]);
    }

    public function obtenhaMatriz(): Matriz
    {
        return $this->matriz;
    }

    public function obtenhaOrigem(): Ponto
    {
        return $this->origem;
    }

    public function escalar(float $escala): void
    {
        $this->escala = $escala;
    }

    public function inversa(): self
    {
        $inversa = MatrizInversa::Inverter($this->matriz);

        return new self($inversa, $this->dePonto($this->origem));
    }

    public function multiplicar(self $transformacao): self
    {
        $matriz = clone $transformacao->matriz;
        $matriz->adicionarLinha([0, 0, 0]);
        $matriz->adicionarColuna([$transformacao->origem->x, $transformacao->origem->y, $transformacao->origem->z, 1]);

        $matrizOriginal = clone $this->matriz;
        $matrizOriginal->adicionarLinha([0, 0, 0]);
        $matrizOriginal->adicionarColuna([$this->origem->x, $this->origem->y, $this->origem->z, 1]);

        $nova = $matriz->Multiplicar($matrizOriginal);
        $x = $nova[0][3];
        $y = $nova[1][3];
        $z = $nova[2][3];
        $origem = new Ponto($x, $y, $z);

        $matrizNova = [$nova[0], $nova[1], $nova[2]];
        unset($matrizNova[0][3], $matrizNova[1][3], $matrizNova[2][3]);

        $retorno = new self(new Matriz($matrizNova), $origem);
        $retorno->escalar($transformacao->escala * $this->escala);
        $mirror = ($this->mirror ? -1 : 1) * ($transformacao->mirror ? -1 : 1) * -1;
        $retorno->mirror = -1 == $mirror ? false : true;

        return $retorno;
    }

    public function escalaBase(float $escala): self
    {
        $matriz = clone $this->matriz;
        $matriz = $matriz->Escalar($escala);

        return new self($matriz, $this->origem);
    }

    public function escalaBaseOrigem(float $escala): self
    {
        $matriz = clone $this->matriz;
        $matriz = $matriz->Escalar($escala);
        $origem = VetorFabrica::apartirPonto($this->origem);
        $origem = $origem->escalar($escala);

        return new self($matriz, new Ponto($origem->x, $origem->y, $origem->z));
    }

    public function anguloTransformacao(): float
    {
        $ponto = VetorFabrica::BaseX();
        $pontoA = $this->deVetor($ponto);

        return acos($pontoA->x);
    }
}
