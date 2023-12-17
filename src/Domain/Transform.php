<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Domain;

use Solidbase\Geometry\Application\Transform\Transform as ModificadoresTransform;

interface Transform
{
    public function move(float|int $dx, float|int $dy, float|int $dz = 0): static;

    public function rotate(float|int $angulo, ?Point $ponto = null): static;

    public function applyScale(float|int $escala, ?Point $ponto = null): static;

    public function applyMirror(Plane|Line $planoOuLinha): static;

    public function applyTransform(ModificadoresTransform $transformacao): static;
}
