<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Domain;

use DomainException;
use Exception;
use Solidbase\Geometry\Application\Transform\Transform;

final class Vector extends Point
{
    public function applyTransform(Transform $transformacao): static
    {
        $novo = $transformacao->applyToVector($this);
        $this->_x = $novo->_x;
        $this->_y = $novo->_y;
        $this->_z = $novo->_z;

        return $this;
    }

    public function hasSameDirection(self $vector): bool
    {
        if ($vector->isZero() || $this->isZero()) {
            return false;
        }
        if ($this->getUnitary()->isEquals($vector->getUnitary())) {
            return true;
        }
        if ($this->getUnitary()->scalar(-1)->isEquals($vector->getUnitary())) {
            return true;
        }

        return false;
    }

    public function hasSameSense(self $vector): bool
    {
        if ($vector->isZero() || $this->isZero()) {
            return false;
        }

        return $this->getUnitary()->difference($vector->getUnitary())->isZero();
    }

    public function product(self $vetor): float
    {
        $x = ($vetor->_x * $this->_x);
        $y = ($vetor->_y * $this->_y);
        $z = ($vetor->_z * $this->_z);

        return $x + $y + $z;
    }

    public function module(): float
    {
        $x = $this->_x ** 2;
        $y = ($this->_y ** 2);
        $z = ($this->_z ** 2);
        $modulo = sqrt($x + $y + $z);

        return sbNormalize($modulo);
    }

    public function scalar(float|int $fator): static
    {
        $x = ($this->_x * $fator);
        $y = ($this->_y * $fator);
        $z = ($this->_z * $fator);

        return new static($x, $y, $z);
    }

    public function getUnitary(): static
    {
        if ($this->isZero()) {
            throw new Exception('Vetores nulos não possui vetor unitário');
        }
        $modulo = $this->module();

        return $this->scalar(1 / $modulo);
    }

    public function getAngle(self $vetor): float
    {
        if ($this->isZero() || $vetor->isZero()) {
            throw new Exception("Nenhum dos vetores podem ser nulos");
        }
        $angulo = ($this->product($vetor) / ($this->module() * $vetor->module()));
        $retorno = acos($angulo);

        return sbNormalize($retorno);
    }

    public function getAbsoluteAngle(): float
    {
        $angulo = atan2($this->_y, $this->_x);
        $angulo += sbLessThan($angulo, 0) ? 2 * M_PI : 0;

        return sbNormalize($angulo);
    }

    public function crossProduct(self $vetor): static
    {
        $x = (($this->_y * $vetor->_z) - ($this->_z * $vetor->_y));
        $y = (($this->_z * $vetor->_x) - ($this->_x * $vetor->_z));
        $z = (($this->_x * $vetor->_y) - ($this->_y * $vetor->_x));

        return new static($x, $y, $z);
    }

    public function tripleProduct(self $vetorV, self $vetorW): float
    {
        return ($this->crossProduct($vetorV))->product($vetorW);
    }

    public function getProjection(self $vetorU): static
    {
        if ($this->isZero()) {
            throw new Exception('Não é possível encontrar projeção em vetores nulos');
        }
        if ($vetorU->isZero()) {
            return new static();
        }
        $modulo = $this->module();
        $escalar = $this->product($vetorU) / $modulo ** 2;

        return $this->scalar($escalar);
    }

    public function isZero(): bool
    {
        $modulo = $this->module();

        return sbIsZero($modulo);
    }

    public static function CreateFromPoint(Point $point): Vector
    {
        return new Vector($point->_x, $point->_y, $point->_z);
    }

    public static function CreateFromPoints(Point $point1, Point $point2): Vector
    {
        $ponto = $point2->difference($point1);

        return self::CreateFromPoint($ponto);
    }

    public static function CreateBaseX(): Vector
    {
        return new Vector(1, 0, 0);
    }

    public static function CreateBaseY(): Vector
    {
        return new Vector(0, 1, 0);
    }

    public static function CreateBaseZ(): Vector
    {
        return new Vector(0, 0, 1);
    }

    public static function CreatePerpendicularVector(Vector $vetor): Vector
    {
        if ($vetor->isZero()) {
            throw new DomainException('Não é possível gerar vetor perpendiculares a partir de vetores nulos');
        }
        $baseZ = self::CreateBaseZ();
        if ($vetor->hasSameDirection($baseZ)) {
            return self::CreateBaseX();
        }

        return $vetor->crossProduct($baseZ);
    }

    public static function CreateVectorFromAngleAndMagnitude(float $angulo, float $modulo = 1): Vector
    {
        $x = cos($angulo) * $modulo;
        $y = sin($angulo) * $modulo;

        return new Vector($x, $y);
    }
}
