<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Aplicacao\Offset;

use DomainException;
use Solidbase\Geometria\Aplicacao\Interseccao\InterseccaoLinhaArco;
use Solidbase\Geometria\Aplicacao\Interseccao\InterseccaoLinhas;
use Solidbase\Geometria\Aplicacao\Offset\Enum\DirecaoOffsetPoligono;
use Solidbase\Geometria\Aplicacao\Offset\Enum\DirecaoOffsetReta;
use Solidbase\Geometria\Aplicacao\Poligono\ArcoConcordanciaPoligono;
use Solidbase\Geometria\Aplicacao\Poligono\PropriedadePoligono;
use Solidbase\Geometria\Aplicacao\Poligono\TipoPoligonoEnum;
use Solidbase\Geometria\Aplicacao\Pontos\RotacaoPontoEnum;
use Solidbase\Geometria\Aplicacao\Pontos\SentidoRotacaoTresPontos;
use Solidbase\Geometria\Dominio\Arco;
use Solidbase\Geometria\Dominio\Fabrica\LinhaFabrica;
use Solidbase\Geometria\Dominio\Fabrica\PolilinhaFabrica;
use Solidbase\Geometria\Dominio\Linha;
use Solidbase\Geometria\Dominio\Polilinha;
use Solidbase\Geometria\Dominio\PontoPoligono;

class OffsetPoligono
{
    private function __construct()
    {
    }

    public static function executar(float|int $offset, Polilinha $polilinha, DirecaoOffsetPoligono $direcao): Polilinha
    {
        $poligono = self::limparPoligono($polilinha);
        $propriedade = PropriedadePoligono::executar($poligono);
        if (TipoPoligonoEnum::Concavo == $propriedade->tipo) {
            throw new DomainException('O algoritmo só funciona em polígonos convexos');
        }
        $offsetLinha = DirecaoOffsetReta::tryFrom($propriedade->sentido * $direcao->value);

        $numeroPonto = \count($poligono);
        $pontos = $poligono->pontos();
        $linhas = [];
        for ($i = 1; $i < $numeroPonto; ++$i) {
            /**
             * @var PontoPoligono
             */
            $p1 = $pontos[$i - 1];

            /**
             * @var PontoPoligono
             */
            $p2 = $pontos[$i];
            $linha = LinhaFabrica::apartirDoisPonto($p2, $p1);
            if (DirecaoOffsetPoligono::Interno == $direcao && eMenor($linha->distanciaPontoLinha($propriedade->centro), $offset)) {
                throw new DomainException('Não é possível gerar offset');
            }
            $linhaOffset = OffsetLinha::executar($offset, $linha, $offsetLinha);
            $linhas[] = $linhaOffset;
            if (!eZero($p2->concordancia)) {
                $p3 = $pontos[$i + 1] ?? $pontos[0];
                $arco = ArcoConcordanciaPoligono::executar($p2, $p3);
                $arcoNovo = OffsetArco::executar($offset, $arco, $direcao);
                $p2->informarConcordancia(0);

                $linhas[] = $arcoNovo;
                ++$i;
            }
        }

        return self::gerarPoligonoOffset($linhas, $polilinha->ePoligono(), RotacaoPontoEnum::tryFrom($propriedade->sentido));
    }

    private static function limparPoligono(Polilinha $polilinha): Polilinha
    {
        $pontos = PolilinhaFabrica::limparPontosPoligono($polilinha->pontos());

        return PolilinhaFabrica::criarPolilinhaPontos($pontos, fechado: $polilinha->ePoligono());
    }

    private static function gerarPoligonoOffset(array $linhas, bool $ePoligono, RotacaoPontoEnum $rotacao): Polilinha
    {
        $numeroLinha = \count($linhas);
        $pontos = [];
        for ($i = 1; $i < $numeroLinha; ++$i) {
            $linha1 = $linhas[$i - 1];
            $linha2 = $linhas[$i];
            if (is_a($linha2, Arco::class)) {
                $pontosArco = self::pontosArco($linha2, $linha1, $rotacao);
                if (null === $pontosArco) {
                    unset($linhas[$i]);

                    return self::gerarPoligonoOffset(array_values($linhas), $ePoligono, $rotacao);
                }
                $pontos[] = $pontosArco;

                continue;
            }
            if (is_a($linha1, Arco::class)) {
                $pontosArco = self::pontosArco($linha1, $linha2, $rotacao);
                if (null === $pontosArco) {
                    unset($linhas[$i - 1]);

                    return self::gerarPoligonoOffset(array_values($linhas), $ePoligono, $rotacao);
                }
                $pontosArco->informarConcordancia(0);
                $pontos[] = $pontosArco;

                continue;
            }

            $ponto = InterseccaoLinhas::executar($linha1, $linha2);
            $rotacaoNova = SentidoRotacaoTresPontos::executar($linha1->origem, $ponto, $linha2->final);
            if ($rotacao != $rotacaoNova) {
                unset($linhas[$i]);

                return self::gerarPoligonoOffset(array_values($linhas), $ePoligono, $rotacao);
            }
            $pontos[] = $ponto;
        }
        if ($ePoligono) {
            $primeiroPonto = InterseccaoLinhas::executar($linhas[0], $linhas[$numeroLinha - 1]);
            array_unshift($pontos, $primeiroPonto);

            return PolilinhaFabrica::criarPolilinhaPontos($pontos, fechado: true);
        }
        $primeiroPonto = RotacaoPontoEnum::HORARIO == $rotacao ? $linhas[0]->origem : $linhas[0]->final;
        $ultimoPonto = RotacaoPontoEnum::HORARIO == $rotacao ? $linhas[$numeroLinha - 1]->final : $linhas[$numeroLinha - 1]->origem;
        array_unshift($pontos, $primeiroPonto);
        $pontos[] = $ultimoPonto;

        return PolilinhaFabrica::criarPolilinhaPontos($pontos);
    }

    private static function pontosArco(Arco $arco, Linha $linha, RotacaoPontoEnum $rotacao): ?PontoPoligono
    {
        if (eZero($arco->raio)) {
            return null;
        }
        [$p1] = InterseccaoLinhaArco::executar($linha, $arco);
        $angulo = $arco->anguloTotal();

        $bulge = abs(tan($angulo * 0.25));
        $p1 = new PontoPoligono($p1->x, $p1->y);
        $p1->informarConcordancia($bulge * $rotacao->value);

        return $p1;
    }
}
