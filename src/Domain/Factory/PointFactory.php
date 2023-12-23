<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Domain\Factory;

use Solidbase\Geometry\Domain\Point;

class PointFactory
{
    public static function CreateFromJson(string|array $data): Point
    {
        $data = is_string($data) ? json_decode($data, true) : $data;

        return new Point($data['x'], $data['y'], $data['z']);
    }
}
