<?php


namespace Application\Controller\Mobile;


use Application\Debug\UtilsFile;
use Application\Repository\MobileRepository;
use Exception;
use Zend\Json\Json;
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
        $infos = $this->mobileManager->getProfissinais();
//        UtilsFile::printvardie($infos);
        foreach ($infos as $info) {
            $results[] = [
                'u_id' => $info['u_id'],
                'info_user_name' => $info['info_user_name'],
                'info_user_addr' => Json::decode($info['pi_prof_addresses'])[0],
                'ue_desc_especialidade' => $info['pi_id_especiality'] == null ? "Aguardando verificação" : $info['ue_desc_especialidade'],
                'confirmed' => $info['pi_confirmed_in'] == null ? false : true
            ];
        }
        return new JsonModel($results);
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
        $infos[0]['pi_prof_addresses'] = Json::decode($infos[0]['pi_prof_addresses']);
        $infos[0]['esp_desc_especialidade'] = $infos[0]['pi_id_especiality'] == null ? "Aguardando verificação" : $infos[0]['esp_desc_especialidade'];
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