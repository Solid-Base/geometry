<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Application\Polygon;

use Solidbase\Geometry\Domain\Point;

class Triangle
{
    public function __construct(private Point $p1, private Point $p2, private Point $p3) {}

    public function getCenter(): Point
    {
        $x = $this->p1->x + $this->p2->x + $this->p3->x;
        $y = $this->p1->y + $this->p2->y + $this->p3->y;

        return new Point($x, $y);
    }

    public function getArea(): float
    {
        $area = (
            $this->p1->x * $this->p2->y -
            $this->p1->y * $this->p2->x -
            $this->p1->y * $this->p3->x -
            $this->p1->x * $this->p3->y +
            $this->p2->x * $this->p3->y -
            $this->p3->x * $this->p2->y
        ) / 2;

        return abs($area);
    }
}
