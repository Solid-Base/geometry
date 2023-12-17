<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Application\Transform;

use Solidbase\Geometry\Application\Transform\Factory\FactoryMatrizTranform;
use Solidbase\Geometry\Domain\Factory\VectorFactory;
use Solidbase\Geometry\Domain\Plane;
use Solidbase\Geometry\Domain\Point;
use Solidbase\Geometry\Domain\Vector;
use SolidBase\Math\Algebra\FabricaMatriz;
use SolidBase\Math\Algebra\InverseMatrix;
use SolidBase\Math\Algebra\Matriz;

class Transform
{
    private bool $mirror = false;
    private Matriz $matriz;
    private Point $origin;
    private float $scale = 1;

    private function __construct(Matriz $matriz, Point $origin)
    {
        $this->matriz = $matriz;
        $this->origin = $origin;
    }

    public function __clone()
    {
        $this->matriz = clone $this->matriz;
        $this->origin = clone $this->origin;
    }

    public static function CreateRotation(Vector $eixo, float $angulo): self
    {
        return new self(FactoryMatrizTranform::CreateMatrizRotation($eixo, $angulo), new Point());
    }

    public static function CreateRotationAroundPoint(Vector $eixo, float|int $angulo, Point $origem): self
    {
        $matriz = FactoryMatrizTranform::CreateMatrizRotation($eixo, $angulo);
        $matrizRotacao = new self($matriz, new Point());
        if ($origem->isEquals(new Point())) {
            return $matrizRotacao;
        }
        $vetor = VectorFactory::FromPoint($origem);
        $translacao = $vetor->scalar(-1);
        $matrizTLinha = self::CreateTranslation($translacao);
        $matrizT = self::CreateTranslation($vetor);
        $primeira = $matrizTLinha->multiply($matrizRotacao);

        return $primeira->multiply($matrizT);
    }

    /**
     * Transformação de Householder
     * A=I-2NN^t
     * N = Vetor Normal
     * I = Matriz Identidade.
     */
    public static function CreateReflection(Plane $plano): self
    {
        $normal = $plano->normal;
        $origem = VectorFactory::FromPoint($plano->origin);
        $d = $origem->scalar(-1)->product($normal);
        $x = -2 * $normal->_x * $d;
        $y = -2 * $normal->_y * $d;
        $z = -2 * $normal->_z * $d;
        $origem = new Point($x, $y, $z);
        $matriz = FactoryMatrizTranform::CreateMatrixReflection($plano);

        $retorno = new self($matriz, $origem);
        $retorno->mirror = true;

        return $retorno;
    }

    public static function CreateTranslation(Point $vetor): self
    {
        return new self(FabricaMatriz::Identity(3), $vetor);
    }

    public static function CreateScale(float|Vector $escala): self
    {
        $identidade = self::CreateMatrizFromEscala($escala);
        $retorno = new self($identidade, new Point());
        $retorno->scale = is_float($escala) ? $escala : abs($escala->_x);

        return $retorno;
    }

    public static function CreateScaleAroundPoint(float|Vector $escala, Point $ponto): self
    {
        if ($ponto->isEquals(new Point())) {
            return self::CreateScale($escala);
        }
        $matrizEscala = self::CreateScale($escala);

        $vetor = VectorFactory::FromPoint($ponto);
        $translacao = $vetor->scalar(-1);
        $matrizTLinha = self::CreateTranslation($translacao);

        $matrizT = self::CreateTranslation($vetor);
        $primeira = $matrizTLinha->multiply($matrizEscala);

        return $primeira->multiply($matrizT);
    }

    public function applyToPoint(Point $ponto): Point
    {
        $matriz = clone $this->matriz;
        if (1 != $this->scale) {
            $matriz = $matriz->scalar($this->scale);
        }
        $matriz->addRow([0, 0, 0]);
        $matriz->addCol([[$this->origin->_x], [$this->origin->_y], [$this->origin->_z], [1]]);
        $pontoM = new Matriz([[$ponto->_x], [$ponto->_y], [$ponto->_z], [1]]);
        $pontoT = $matriz->multiply($pontoM);

        return new Point($pontoT['1'], $pontoT['2'], $pontoT['3']);
    }

    public function applyToVector(Vector $vetor): Vector
    {
        $matriz = clone $this->matriz;
        $pontoM = new Matriz([[$vetor->_x], [$vetor->_y], [$vetor->_z]]);
        $pontoT = $matriz->multiply($pontoM);

        return new Vector($pontoT['1'], $pontoT['2'], $pontoT['3']);
    }

    public function getMatriz(): Matriz
    {
        return $this->matriz;
    }

    public function getOrigin(): Point
    {
        return $this->origin;
    }

    public function setScale(float $escala): void
    {
        $this->scale = $escala;
    }

    public function inverse(): self
    {
        $inversa = InverseMatrix::Inverse($this->matriz);

        return new self($inversa, $this->applyToPoint($this->origin));
    }

    public function multiply(self $transformacao): self
    {
        $matriz = clone $transformacao->matriz;
        if (1 != $transformacao->scale) {
            $matriz = $matriz->scalar($transformacao->scale);
        }
        $matriz->addRow([0, 0, 0]);
        $matriz->addCol([[$transformacao->origin->_x], [$transformacao->origin->_y], [$transformacao->origin->_z], [1]]);

        $matrizOriginal = clone $this->matriz;
        if (1 != $this->scale) {
            $matrizOriginal = $matrizOriginal->scalar($this->scale);
        }
        $matrizOriginal->addRow([0, 0, 0]);
        $matrizOriginal->addCol([[$this->origin->_x], [$this->origin->_y], [$this->origin->_z], [1]]);

        $nova = $matriz->multiply($matrizOriginal);
        $x = $nova['1,4'];
        $y = $nova['2,4'];
        $z = $nova['3,4'];
        $origem = new Point($x, $y, $z);

        $linha1 = [$nova['1,1'], $nova['1,2'], $nova['1,3']];
        $linha2 = [$nova['2,1'], $nova['2,2'], $nova['2,3']];
        $linha3 = [$nova['3,1'], $nova['3,2'], $nova['3,3']];

        $matrizNova = [$linha1, $linha2, $linha3];
        $matrizNova = new Matriz($matrizNova);
        $escalaFinal = $transformacao->scale * $this->scale;
        if (1 != $escalaFinal) {
            $matrizNova = $matrizNova->scalar(1 / $escalaFinal);
        }
        $retorno = new self($matrizNova, $origem);
        $retorno->scale = ($transformacao->scale * $this->scale);
        $mirror = ($this->mirror ? -1 : 1) * ($transformacao->mirror ? -1 : 1) * -1;
        $retorno->mirror = -1 == $mirror ? false : true;

        return $retorno;
    }

    public function applyScaleBase(float $escala): self
    {
        $matriz = clone $this->matriz;
        $matriz = $matriz->scalar($escala);

        return new self($matriz, $this->origin);
    }

    public function applyScaleBaseAroundOrigin(float $escala): self
    {
        $matriz = clone $this->matriz;
        $matriz = $matriz->scalar($escala);
        $origem = VectorFactory::FromPoint($this->origin);
        $origem = $origem->scalar($escala);

        return new self($matriz, new Point($origem->_x, $origem->_y, $origem->_z));
    }

    public function getTranformAngle(): float
    {
        $ponto = VectorFactory::CreateBaseX();
        $pontoA = $this->applyToVector($ponto);

        return $pontoA->getAbsoluteAngle();
    }

    public function getScale(): int|float
    {
        return $this->scale;
    }

    public function isReflection(): bool
    {
        return $this->mirror;
    }

    private static function CreateMatrizFromEscala(float|Vector $escala): Matriz
    {
        if (is_float($escala)) {
            return FabricaMatriz::Identity(3);
        }

        return new Matriz([[$escala->_x, 0, 0], [0, $escala->_y, 0], [0, 0, $escala->_z]]);
    }
}
