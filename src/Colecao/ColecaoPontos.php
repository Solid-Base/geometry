<?php

declare(strict_types=1);

namespace Solidbase\Geometria\Colecao;

use Solidbase\Geometria\Aplicacao\Interseccao\InterseccaoLinhas;
use Solidbase\Geometria\Dominio\Fabrica\LinhaFabrica;
use Solidbase\Geometria\Dominio\Fabrica\VetorFabrica;
use Solidbase\Geometria\Dominio\Linha;
use Solidbase\Geometria\Dominio\Ponto;
use SolidBase\Utilitarios\Colecao\Colecao;
use SolidBase\Utilitarios\Colecao\UniqueInterface;

/**
 * @method void       offsetSet(int $offset, Ponto $value)
 * @method Ponto      offsetGet(int $offset)
 * @method bool|Ponto current()
 * @method ?Ponto     primeiro(?Closure $filtro)
 * @method ?Ponto     ultimo(?Closure $filtro)
 * @method bool       existe(Ponto $objeto)
 */
class ColecaoPontos extends Colecao implements UniqueInterface
{
    public function __construct()
    {
        parent::__construct();
        $this->tipoObjeto = Ponto::class;
    }

    public function existe(mixed $objeto): bool
    {
        if (!$this->objetoEValido($objeto)) {
            return false;
        }
        $filtro = $this->primeiro(fn (Ponto $p) => eZero($p->distanciaParaPonto($objeto)));

        return null !== $filtro;
    }

    public function unique(): static
    {
        $pontosRetorno = new static();
        foreach ($this as $ponto) {
            if ($pontosRetorno->existe($ponto)) {
                continue;
            }
            $pontosRetorno->adicionar($ponto);
        }

        return $pontosRetorno;
    }

    public function ordenarPontosLinha(Linha $linha): static
    {
        $direcaoOrtogonal = VetorFabrica::Perpendicular($linha->direcao);
        $pontos = $this->map(fn (Ponto $p) => InterseccaoLinhas::executar($linha, LinhaFabrica::origemDirecao($p, $direcaoOrtogonal)));
        $direcaoReta = $linha->direcao;
        $origemOriginal = $linha->origem;
        $pontosOrigem = $pontos->filtrar(fn (Ponto $p) => !(VetorFabrica::apartirDoisPonto($origemOriginal, $p)->temMesmoSentido($direcaoReta)));
        if (count($pontosOrigem) > 0) {
            $distancias = $pontosOrigem->map(fn (Ponto $p) => $p->distanciaParaPonto($origemOriginal));
            $max = max($distancias);
            $key = array_search($max, $distancias);
            $origemOriginal = $pontosOrigem[$key];
        }
        $distancias = $pontos->map(fn (Ponto $p) => $p->distanciaParaPonto($origemOriginal));
        asort($distancias);
        $indices = array_keys($distancias);
        $retorno = array_map(fn (int $k) => $this[$k], $indices);

        return static::deArray($retorno);
    }

    public function uniao(ColecaoPontos|array $pontos): static
    {
        foreach ($pontos as $ponto) {
            if (!$this->existe($ponto)) {
                $this->adicionar($ponto);
            }
        }

        return $this;
    }

    public function enumerarIndices(): static
    {
        $this->objetos = array_values($this->objetos);
        $this->index = 0;

        return $this;
    }
}
