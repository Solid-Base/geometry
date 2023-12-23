<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Domain;

use Countable;
use DomainException;
use JsonSerializable;
use Solidbase\Geometry\Application\Transform\Transform;
use Solidbase\Geometry\Collection\PointCollection;
use Solidbase\Geometry\Domain\Trait\TransformTrait;
use Solidbase\Geometry\Domain\Transform as DomainTransform;

class Polyline implements Countable, JsonSerializable, DomainTransform
{
    use TransformTrait;

    private PointCollection $points;

    public function __construct(private bool $closed = false)
    {
        $this->points = new PointCollection();
    }

    public function __clone()
    {
        $this->points = clone $this->points;
    }

    public function __serialize(): array
    {
        $pontos = $this->points->map(fn(Point $p) => serialize($p));

        return ['pontos' => $pontos];
    }

    public function __unserialize(array $data): void
    {
        $pontos = $data['pontos'];
        $pontos = array_map(fn(string $p) => unserialize($p), $pontos);
        $this->points = new PointCollection($pontos);
    }

    public function applyTransform(Transform $transformacao): static
    {
        $this->points->map(fn(Point $p) => $p->applyTransform($transformacao));
        if ($transformacao->isReflection()) {
            $this->points->map(fn(PointOfPolygon $p) => $p->setAgreement($p->agreement * -1));
        }

        return $this;
    }

    public function jsonSerialize(): mixed
    {
        return $this->points;
    }

    public function count(): int
    {
        $total = count($this->points);
        $total += $this->closed && !$this->lastEqualPrimary() ? 1 : 0;

        return $total;
    }

    public function addPoint(Point $ponto): self
    {
        if (Point::class === $ponto::class || Vector::class === $ponto::class) {
            $x = $ponto->x;
            $y = $ponto->y;
            $z = $ponto->z;
            $ponto = new PointOfPolygon($x, $y, $z);
        }
        $this->points[] = $ponto;

        return $this;
    }

    public function closePolyline(): self
    {
        if (\count($this->points) <= 2) {
            throw new DomainException('Para fechar uma polilinha, é necessário pelo menos 3 pontos');
        }
        $this->closed = true;

        return $this;
        // if ($this->ePoligono()) {
        //     return $this;
        // }
        // $primeiro = reset($this->pontos);
        // $this->pontos[] = $primeiro;

        // return $this;
    }

    public function isPolygon(): bool
    {
        return $this->closed;
    }

    public function getPoints(): PointCollection
    {
        $retorno = clone $this->points;
        $ultimo = $retorno->first();
        if ($this->closed && !$this->lastEqualPrimary()) {
            $retorno->add($ultimo);
        }

        return $retorno;
    }

    private function lastEqualPrimary(): bool
    {
        $ultimo = $this->points->last();
        $primeiro = $this->points->first();

        return $ultimo->isEquals($primeiro);
    }
}
