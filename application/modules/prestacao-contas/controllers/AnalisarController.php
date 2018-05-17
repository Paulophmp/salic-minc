<?php

class PrestacaoContas_AnalisarController extends MinC_Controller_Action_Abstract
{
    public function init()
    {
        $PermissoesGrupo = [
            Autenticacao_Model_Grupos::TECNICO_PRESTACAO_DE_CONTAS,
            Autenticacao_Model_Grupos::COORDENADOR_PRESTACAO_DE_CONTAS
        ];

        $auth = Zend_Auth::getInstance();

        isset($auth->getIdentity()->usu_codigo) ? parent::perfil(1, $PermissoesGrupo) : parent::perfil(4, $PermissoesGrupo);

        isset($auth->getIdentity()->usu_codigo) ? $this->idUsuario = $auth->getIdentity()->usu_codigo : $this->idUsuario = $auth->getIdentity()->IdUsuario;

        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo');

        if (isset($auth->getIdentity()->usu_codigo)) {
            $this->codGrupo = $GrupoAtivo->codGrupo;
            $this->codOrgao = $GrupoAtivo->codOrgao;
            $this->codOrgaoSuperior = (!empty($auth->getIdentity()->usu_org_max_superior)) ? $auth->getIdentity()->usu_org_max_superior : null;
        }

        parent::init();
    }

    public function comprovanteAction()
    {
        $idPronac = $this->_request->getParam("idPronac");
        $idPlanilhaItem = $this->_request->getParam("idPlanilhaItem");
        $uf = $this->getRequest()->getParam('uf');
        $municipio = $this->getRequest()->getParam('idmunicipio');
        $idPlanilhaEtapa = $this->getRequest()->getParam('idplanilhaetapa');
        $codigoProduto = $this->getRequest()->getParam('produto');
        $stItemAvaliado = $this->getRequest()->getParam('stItemAvaliado');

        $planilhaAprovacaoModel = new PlanilhaAprovacao();

        $projeto = $planilhaAprovacaoModel
            ->vwComprovacaoFinanceiraProjeto(
                $idPronac,
                $uf,
                null,
                /* $codigoProduto != 0 ? $codigoProduto :  null, */
                $codigoProduto,
                $municipio,
                null,
                $idPlanilhaItem
            );

        if (!$projeto) {
            $this->_helper->flashMessengerType->addMessage('ALERT');
            $this->_helper->flashMessenger->addMessage('N&atilde;o houve comprova&ccedil;&atilde;o para este item.');
            $this->redirect("realizarprestacaodecontas/planilhaorcamentaria/idPronac/{$idPronac}");
        } else {
            $this->view->tipoComprovante = $this->tipoDocumento;

            $vwComprovacoes = new PrestacaoContas_Model_vwComprovacaoFinanceiraProjetoPorItemOrcamentario();
            $comprovantes = $vwComprovacoes->comprovacoes(
                    $idPronac, 
                    $idPlanilhaItem, 
                    $stItemAvaliado,
                    $codigoProduto
            );
        }

        $this->view->idPronac = $idPronac;
        $this->view->idPlanilhaItem = $idPlanilhaItem;
        $this->view->idPlanilhaAprovacao = $idPlanilhaAprovacao;
        $this->view->projeto = $projeto[0];
        $this->view->comprovantesPagamento = $comprovantes;
        $this->view->stItemAvaliado = $stItemAvaliado;

        $this->view->uf = $uf;
        $this->view->municipio = $municipio;
        $this->view->idPlanilhaEtapa = $idPlanilhaEtapa;
        $this->view->codigoProduto = $codigoProduto;
    }
}
