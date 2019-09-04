<?php


namespace Application\Controller\Mobile;


use Application\Repository\MobileRepository;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class FrontAppController extends AbstractActionController
{

    protected $mobileManager;

    public function __construct(MobileRepository $mr)
    {
        $this->mobileManager = $mr;
    }

    public function getProfissionaisAction()
    {
        $params = $this->getRequest()->getQuery()->toArray();
        $view = new ViewModel([
            'profissionais' => $this->mobileManager->getProfissinais($params['esp'])
        ]);
        $view->setTerminal(true);
        return $view;
    }

    public function getProfissionalInfoAction()
    {
        $params = $this->params()->fromQuery();
        if (isset($params['proc'])) {
            $procedures = $this->mobileManager->getProceduresProfessional($params['prof']);
            return new JsonModel([
                $procedures
            ]);
        }
        $infos = $this->mobileManager->getProfissionalInfo($params['id_user'], true);
        $infos[0]['procedures'] = $this->mobileManager->getProceduresProfessional($params['id_user']);
        $view = new ViewModel([
            'prof' => $infos
        ]);
        $view->setTerminal(true);
        return $view;
    }

    public function getEspecialidadesAction()
    {
        $esp = $this->mobileManager->getEspecialidade();
        return new JsonModel([
            $esp
        ]);
    }

    public function getLogMessagesAction()
    {
        return new JsonModel([
            'error' => $this->mobileManager->getMessage()
        ]);
    }
}