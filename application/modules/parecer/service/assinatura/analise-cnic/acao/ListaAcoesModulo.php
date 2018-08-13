<?php

namespace Application\Modules\Parecer\Service\Assinatura\AnaliseCNIC;

use MinC\Assinatura\Acao\IListaAcoesModulo;

class ListaAcoesModulo implements IListaAcoesModulo
{

    public function obterLista(): array
    {
        return [
            new Assinar(),
            new Encaminhar(),
            new Devolver(),
            new Finalizar()
        ];
    }
}
