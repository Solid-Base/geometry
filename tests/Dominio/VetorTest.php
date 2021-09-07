<?php

declare(strict_types=1);

namespace Tests\Dominio;

use Exception;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Solidbase\Geometria\Dominio\Vetor;

/**
 * @internal
 * @coversNothing
 */
final class VetorTest extends TestCase
{
    public function testTemMesmaDirecao(): void
    {
        $ponto = new Vetor(0, 10);
        $ponto2 = new Vetor(0, -10);
        $pontoNulo = new Vetor();
        static::assertTrue($ponto->temMesmaDirecao($ponto2));
        static::assertFalse($pontoNulo->temMesmaDirecao($ponto));
        static::assertTrue($ponto->temMesmaDirecao($ponto));
    }

    public function testTemMesmoSentido(): void
    {
        $ponto = new Vetor(0, 10);
        $ponto2 = new Vetor(0, -10);
        $ponto3 = new Vetor(0, 30);

        static::assertTrue($ponto->temMesmoSentido($ponto3));
        static::assertFalse($ponto->temMesmoSentido($ponto2));
    }

    public function testProdutoInterno(): void
    {
        $ponto = new Vetor(5, 10, 5);
        $ponto2 = new Vetor(-4, -10, 6);
        static::assertSame(-90.0, $ponto->produtoInterno($ponto2));
    }

    public function testModulo(): void
    {
        $ponto = new Vetor(3, 4, 0);
        $ponto2 = new Vetor(-12, -16, 5);
        static::assertSame(5.0, $ponto->modulo());
        static::assertSame(20.61552812808830274910704927987, $ponto2->modulo());
    }

    public function testEscalar(): void
    {
        $ponto = new Vetor(3, 4, 0);
        $ponto2 = new Vetor(-12, -16, 4);
        static::assertTrue($ponto->escalar(2)->eIgual(new Vetor(6, 8, 0)));
        static::assertTrue($ponto2->escalar(3)->eIgual(new Vetor(-36, -48, 12)));
    }

    public function testVetorUnitario(): void
    {
        $vetor = new Vetor();
        $vetor2 = new Vetor(3, 4, 8);
        static::assertTrue(
            $vetor2->vetorUnitario()->eIgual(new Vetor(
                0.31799936400190799364002225991987,
                0.42399915200254399152002967989327,
                0.84799830400508798304005935978654
            ))
        );
        $this->expectException(Exception::class);
        $vetor->vetorUnitario();
    }

    public function testAngulo(): void
    {
        $vetor = new Vetor(10, 0);
        $vetor2 = new Vetor(-10, 0);
        $vetor3 = new Vetor(4, 4, 0);
        $vetor4 = new Vetor(-4, 4, 0);

        static::assertSame(M_PI, $vetor->angulo($vetor2));
        static::assertSame(M_PI / 4, $vetor->angulo($vetor3));
        static::assertSame(M_PI / 4 + M_PI / 2, $vetor->angulo($vetor4));
    }

    public function testAnguloAbsoluto(): void
    {
        $vetor1 = new Vetor(-4, -4, 0);
        $vetor3 = new Vetor(4, 4, 0);
        $vetor4 = new Vetor(-4, 4, 0);
        $vetor5 = new Vetor(4, -4, 0);

        static::assertSame(M_PI + M_PI / 4, $vetor1->anguloAbsoluto());
        static::assertSame(M_PI / 4, $vetor3->anguloAbsoluto());
        static::assertSame(M_PI / 2 + M_PI / 4, $vetor4->anguloAbsoluto());
        static::assertSame(M_PI / 2 + M_PI / 4 + M_PI, $vetor5->anguloAbsoluto());
    }

    public function testProdutoVetorial(): void
    {
        $vetor = new Vetor(2, 4, 3);
        $vetor2 = new Vetor(3, 5, 7);
        $resposta = new Vetor(13, -5, -2);
        static::assertTrue($resposta->eIgual($vetor->produtoVetorial($vetor2)));
    }

    public function testProdutoMisto(): void
    {
        $vetor = new Vetor(2, 4, 3);
        $vetor2 = new Vetor(3, 5, 7);
        $vetor3 = new Vetor(4, 3, 1);
        static::assertSame(35.0, $vetor->produtoMisto($vetor2, $vetor3));
    }

    public function testProjecao(): void
    {
        $vetor = new Vetor(-2, -3, 4);
        $vetor2 = new Vetor(2, -1, 3);

        $modulo = 11 / 29;
        $resposta = new Vetor(-2 * $modulo, -3 * $modulo, 4 * $modulo);

        static::assertTrue($resposta->eIgual($vetor->projecao($vetor2)));
        $vetor3 = new Vetor();
        static::assertTrue((new Vetor())->eIgual($vetor->projecao($vetor3)));
        $this->expectException(Exception::class);
        $vetor3->projecao($vetor);
    }

    public function testENulo(): void
    {
        $reflexao = new ReflectionClass(Vetor::class);
        $metodoENulo = $reflexao->getMethod('eNulo');
        $metodoENulo->setAccessible(true);
        $vetor = new Vetor();
        $vetor2 = new Vetor(2, 5, 8);

        static::assertTrue($metodoENulo->invoke($vetor));
        static::assertFalse($metodoENulo->invoke($vetor2));
    }
}
