<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Application\Intersector;

use DomainException;
use Exception;
use Solidbase\Geometry\Domain\Arc;
use Solidbase\Geometry\Domain\Circle;
use Solidbase\Geometry\Domain\Line;

class LineArcIntersector
{
    private function __construct() {}

    public static function tryCalculate(Line $line, Arc $arc): ?array
    {
        try {
            return self::calculate($line, $arc);
        } catch(Exception) {
            return null;
        }
    }
    public static function calculate(Line $linha, Arc $arco): array
    {
        $circulo = new Circle($arco->center, $arco->radius);

        if(!LineCircleIntersector::CheckLineCircleIntersection($linha, $circulo)) {
            throw new DomainException("Line not intersect Arc");
        }
        $results = LineCircleIntersector::Calculate($linha, $circulo);
        $points = [];
        foreach($results as $p) {
            if(!$arco->pointBelongsToArc($p)) {
                continue;
            }
            array_push($points, $p);
        }

        if(count($points) === 0) {
            throw new DomainException("Line not intersect Arc");
        }
        return $points;
    }
}
