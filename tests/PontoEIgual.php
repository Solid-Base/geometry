<?php

declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests;

use function get_class;
use function is_object;
use PHPUnit\Framework\Constraint\Constraint;
use PHPUnit\Framework\ExpectationFailedException;
use SebastianBergmann\Comparator\ComparisonFailure;
use Solidbase\Geometria\Dominio\Ponto;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final class PontoEIgual extends Constraint
{
    /**
     * @var float
     */
    private const EPSILON = 0.0000000001;

    /**
     * @var Ponto
     */
    private $value;

    public function __construct(Ponto $value)
    {
        $this->value = $value;
    }

    /**
     * Evaluates the constraint for parameter $other.
     *
     * If $returnResult is set to false (the default), an exception is thrown
     * in case of a failure. null is returned otherwise.
     *
     * If $returnResult is true, the result of the evaluation is returned as
     * a boolean value instead: true in case of success, false in case of a
     * failure.
     *
     * @param Ponto $other
     *
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws ExpectationFailedException
     */
    public function evaluate($other, string $description = '', bool $returnResult = false): ?bool
    {
        $x = abs($this->value->x - $other->x);
        $y = abs($this->value->y - $other->y);
        $z = abs($this->value->z - $other->z);

        $success = eZero($x) && eZero($y) && eZero($z);

        if ($returnResult) {
            return $success;
        }

        if (!$success) {
            $f = null;
            $valor = [$this->value->x, $this->value->x, $this->value->x];
            $outro = [$other->x, $other->y, $other->z];
            $f = new ComparisonFailure(
                $valor,
                $outro,
                $this->exporter()->export($valor),
                $this->exporter()->export($outro)
            );
            $this->fail($other, $description, $f);
        }

        return null;
    }

    /**
     * Returns a string representation of the constraint.
     *
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function toString(): string
    {
        if (is_object($this->value)) {
            return 'is identical to an object of class "'.
                get_class($this->value).'"';
        }

        return 'is identical to '.$this->exporter()->export($this->value);
    }

    /**
     * Returns the description of the failure.
     *
     * The beginning of failure messages is "Failed asserting that" in most
     * cases. This method should return the second part of that sentence.
     *
     * @param mixed $other evaluated value or object
     *
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    protected function failureDescription($other): string
    {
        return 'Os pontos ou vetores informados não são iguais';
    }
}
