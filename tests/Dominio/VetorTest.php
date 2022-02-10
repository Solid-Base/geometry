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
        static::assertTrue($ponto->produtoInterno($ponto2)->eIgual(-90));
    }

    public function testModulo(): void
    {
        $ponto = new Vetor(3, 4, 0);
        $ponto2 = new Vetor(-12, -16, 5);
        static::assertTrue($ponto->modulo()->eIgual(5));
        static::assertTrue($ponto2->modulo()->eIgual(numero('20.61552812808830274910704927987')));
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
        static::assertTrue(
            $vetor->angulo($vetor2)->arredondar(PRECISAO_SOLIDBASE)->eIgual(numero(S_PI)->arredondar(PRECISAO_SOLIDBASE))
        );
        static::assertTrue(
            $vetor->angulo($vetor3)->arredondar(PRECISAO_SOLIDBASE)->eIgual(dividir(S_PI, 4)->arredondar(PRECISAO_SOLIDBASE))
        );
        static::assertTrue(
            $vetor->angulo($vetor4)->arredondar(PRECISAO_SOLIDBASE)->eIgual(somar(dividir(S_PI, 4), dividir(S_PI, 2))->arredondar(PRECISAO_SOLIDBASE))
        );
        // static::assertSame(arredondar(M_PI, 5), arredondar($vetor->angulo($vetor2), 5));
        // static::assertSame(arredondar(M_PI / 4, 5), arredondar($vetor->angulo($vetor3), 5));
        // static::assertSame(arredondar(M_PI / 4 + M_PI / 2, 5), arredondar($vetor->angulo($vetor4), 5));
    }

    public function testAnguloAbsoluto(): void
    {
        $vetor1 = new Vetor(-4, -4, 0);
        $vetor3 = new Vetor(4, 4, 0);
        $vetor4 = new Vetor(-4, 4, 0);
        $vetor5 = new Vetor(4, -4, 0);

        $angulo = somar(S_PI, dividir(S_PI, 4))->arredondar(PRECISAO_SOLIDBASE);
        static::assertTrue($vetor1->anguloAbsoluto()->arredondar(PRECISAO_SOLIDBASE)->eIgual($angulo));
        $angulo = dividir(S_PI, 4)->arredondar(PRECISAO_SOLIDBASE);
        static::assertTrue($vetor3->anguloAbsoluto()->arredondar(PRECISAO_SOLIDBASE)->eIgual($angulo));
        $angulo = somar(dividir(S_PI, 2), dividir(S_PI, 4))->arredondar(PRECISAO_SOLIDBASE);
        static::assertTrue($vetor4->anguloAbsoluto()->arredondar(PRECISAO_SOLIDBASE)->eIgual($angulo));

        $angulo = somar(somar(dividir(S_PI, 2), dividir(S_PI, 4)), numero(S_PI))->arredondar(PRECISAO_SOLIDBASE);
        static::assertTrue($vetor5->anguloAbsoluto()->arredondar(PRECISAO_SOLIDBASE)->eIgual($angulo));
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
        static::assertTrue($vetor->produtoMisto($vetor2, $vetor3)->eIgual(35));
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
