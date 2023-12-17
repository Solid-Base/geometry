<?php

declare(strict_types=1);

namespace Solidbase\Geometry\Collection;

use Doctrine\Common\Collections\ArrayCollection;
use Solidbase\Geometry\Application\Intersector\LineIntersector;
use Solidbase\Geometry\Domain\Factory\LineFactory;
use Solidbase\Geometry\Domain\Factory\VectorFactory;
use Solidbase\Geometry\Domain\Line;
use Solidbase\Geometry\Domain\Point;

/**
 * @psalm-template TKey of int
 * @psalm-template T of Point
 * @template-implements Collection<TKey,T>
 * @template-implements Selectable<TKey,T>
 * @psalm-consistent-constructor
 */
final class PointCollection extends ArrayCollection
{
    /**
     *
     * @param Point[] $elements
     */
    public function __construct(array $elements = [])
    {
        parent::__construct($elements);

    }


    public function sortPointOfLine(Line $linha): static
    {
        $direcaoOrtogonal = VectorFactory::CreatePerpendicular($linha->_direction);
        $pontos = $this->map(fn(Point $p) => LineIntersector::Calculate($linha, LineFactory::CreateFromOriginAndDirection($p, $direcaoOrtogonal)));
        $direcaoReta = $linha->_direction;
        $origemOriginal = $linha->origin;
        $pontosOrigem = $pontos->filter(fn(Point $p) => !(VectorFactory::CreateFromPoints($origemOriginal, $p)->hasSameSense($direcaoReta)));
        if (count($pontosOrigem) > 0) {
            $distancias = $pontosOrigem->map(fn(Point $p) => $p->distanceToPoint($origemOriginal))->getValues();
            $max = max($distancias);
            $key = array_search($max, $distancias);
            $origemOriginal = $pontosOrigem[$key];
        }
        $distancias = $pontos->map(fn(Point $p) => $p->distanceToPoint($origemOriginal))->getValues();
        asort($distancias);
        $indices = array_keys($distancias);
        $retorno = array_map(fn(int $k) => $this[$k], $indices);

        return new static($retorno);
    }

    public function unique(): static
    {
        $result = new static();

        foreach($this as $point) {
            if($result->exist($point)) {
                continue;
            }
            $result->add($point);
        }
        return $result;
    }
    public function exist(Point $point): bool
    {
        return $this->exists(fn($_, Point $p) => sbIsZero($p->distanceToPoint($point)));
    }

    public function union(PointCollection|array $pontos): static
    {
        foreach ($pontos as $ponto) {
            if (!$this->exist($ponto)) {
                $this->add($ponto);
            }
        }

        return $this;
    }


}
