<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Domain\Trait;

use Solidbase\Geometry\Application\Transform\Transform;
use Solidbase\Geometry\Domain\Factory\VectorFactory;
use Solidbase\Geometry\Domain\Line;
use Solidbase\Geometry\Domain\Plane;
use Solidbase\Geometry\Domain\Point;

trait TransformTrait
{
    public function move(float|int $dx, float|int $dy, float|int $dz = 0): static
    {
        $transformacao = Transform::CreateTranslation(new Point($dx, $dy, $dz));

        return $this->applyTransform($transformacao);
    }

    public function rotate(float|int $angulo, ?Point $ponto = null): static
    {
        if (sbIsZero($angulo)) {
            return $this;
        }
        if (null === $ponto) {
            $transformacao = Transform::CreateRotation(VectorFactory::CreateBaseZ(), $angulo);
        } else {
            $transformacao = Transform::CreateRotationAroundPoint(VectorFactory::CreateBaseZ(), $angulo, $ponto);
        }

        return $this->applyTransform($transformacao);
    }

    public function applyScale(float|int $escala, ?Point $ponto = null): static
    {
        if (null == $ponto || $ponto->isEquals(new Point())) {
            $transformacao = Transform::CreateTranslation(new Point());
            $transformacao->setScale($escala);

            return $this->applyTransform($transformacao);
        }
        $transformacao = Transform::CreateScaleAroundPoint($escala, $ponto);

        return $this->applyTransform($transformacao);
    }

    public function applyMirror(Plane|Line $planoOuLinha): static
    {
        if ($planoOuLinha instanceof Line) {
            $direcao = $planoOuLinha->_direction;
            $normal = $direcao->produtoVetorial(VectorFactory::CreateBaseZ());
            $planoOuLinha = new Plane($planoOuLinha->origin, $normal);
        }
        $transformacao = Transform::CreateReflection($planoOuLinha);

        return $this->applyTransform($transformacao);
    }
}
