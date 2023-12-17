<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Application\Polygon;

use Solidbase\Geometry\Domain\Point;

class Triangle
{
    public function __construct(private Point $p1, private Point $p2, private Point $p3) {}

    public function getCenter(): Point
    {
        $x = $this->p1->_x + $this->p2->_x + $this->p3->_x;
        $y = $this->p1->_y + $this->p2->_y + $this->p3->_y;

        return new Point($x, $y);
    }

    public function getArea(): float
    {
        $area = (
            $this->p1->_x * $this->p2->_y -
            $this->p1->_y * $this->p2->_x -
            $this->p1->_y * $this->p3->_x -
            $this->p1->_x * $this->p3->_y +
            $this->p2->_x * $this->p3->_y -
            $this->p3->_x * $this->p2->_y
        ) / 2;

        return abs($area);
    }
}
