<?php
/**
 * Created by PhpStorm.
 * User: 400005
 * Date: 13/03/2019
 * Time: 09:24
 */

namespace Application\Controller;


use Application\Controller\Repository\MobileRepository;
use Application\Debug\UtilsFile;
use Application\Entity\Seg\User;
use Application\Entity\Sis\UserAppointment;
use Application\Entity\Sis\UserEspeciality;
use Application\Entity\Sis\UserHistoric;
use Application\Entity\Sis\UserHistoricInformation;
use Application\Entity\Sis\UserInfoPessoal;
use Authentication\Service\AuthenticationManager;
use DateInterval;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManager;
use Exception;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\SessionManager;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class MobileController extends AbstractActionController
{

    /** @var $mobileManager MobileRepository */
    protected $mobileManager;
    /** @var $authManager AuthenticationManager */
    protected $authManager;


    /**
     * MobileController constructor.
     * @param $mobileManager
     * @param $authManager
     */
    public function __construct($mobileManager, $authManager)
    {
        $this->mobileManager = $mobileManager;
        $this->authManager = $authManager;
    }

    public function homeAction()
    {
        return new ViewModel([
            'userstate' => $this->authManager->userState()
        ]);
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
        $infos = $this->mobileManager->getProfissionalInfo($params['id_user']);
        $infos[0]['procedures'] = $this->mobileManager->getProceduresProfessional($params['id_user']);
        $view = new ViewModel([
            'prof' => $infos
        ]);
        $view->setTerminal(true);
        return $view;
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

    public function getEspecialidadesAction()
    {
        $esp = $this->mobileManager->getEspecialidade();
        return new JsonModel([
            $esp
        ]);
    }

    public function getScheduleAction()
    {
        $params = $this->getRequest()->getQuery()->toArray();
        $resp = [];
        try {
            $resultados = $this->mobileManager->getSchedule($params);
            foreach ($resultados as $appointment) {
                $data = new DateTime($appointment['solicited_for'], new DateTimeZone("America/Belem"));
                $resp[] = [
                    'title' => $this->mobileManager->getAppointmentDescription($appointment['id_procedure']),
                    'start' => $data->format('Y-m-d') . 'T' . $data->format('H:i:s') . '-03:00',
                    'end' => $data->format('Y-m-d') . 'T' . $data->format('H:i:s') . '-03:00'
                ];
            }
        } catch (Exception $e) {
            $resp = ['erro' => $e->getMessage()];
        }
        return new JsonModel(
            $resp
        );
    }

    public function getUserHistoricAction()
    {
        $params = $this->params()->fromQuery();
        $results = $this->mobileManager->getUserHistoric($this->authManager->getActiveUser()['user_id'], $params['type']);
        if (!count($results)) {
            $results = [];
        }
        return new JsonModel($results);
    }

    public function loginFormAction()
    {
        $view = new ViewModel();
        $view->setTerminal(true);
        return $view;
    }

    public function userProfileAction()
    {
        $activeUser = $this->authManager->getActiveUser();
        if ($this->getRequest()->isPost()) {
            $this->mobileManager->updateUserInfo($this->params()->fromPost(), $activeUser['user_id']);
            $this->redirect()->toRoute('home');
        }
        if ($this->params()->fromQuery('json')) {
            $data = $this->mobileManager->getUserInformation($activeUser['user_id'], 3)[0];
            $info = [
                'info_user_name' => $data['info_user_name'],
                'info_user_cpf' => $data['info_user_cpf'],
                'info_user_rg' => $data['info_user_rg'],
                'info_user_healthcare' => $data['info_user_healthcare'],
                'info_user_addr' => $data['info_user_addr'],
                'info_user_ctt_phone' => $data['info_user_ctt_phone'],
                'info_user_ctt_res' => $data['info_user_ctt_res']
            ];
            return new JsonModel($info);
        }
        $userInfo = $this->mobileManager->getUserInformation($activeUser['user_id']);
        $view = new ViewModel([
            'user' => $userInfo[0]
        ]);
        $view->setTerminal(true);
        return $view;
    }

    public function saveAppointAction()
    {
        $params = $this->params()->fromPost();
        $vData = [
            "datareq" => UtilsFile::formataDataToBdSemHora($params['fDataReq']),
            "horareq" => $params['fHoraReq'],
            "proc" => $params['fProcdReq'],
            "user_req" => $this->authManager->getActiveUser()['user_id'],
            "prof_req" => $params['fIdProf']
        ];
//        UtilsFile::printvar($vData);
        try {
            if ($this->mobileManager->saveAppointment($vData)) {
                $this->mobileManager->setMessage("Solicitação salva com sucesso", 1);
                $this->redirect()->toRoute('application_mobile');
            } else {
                //Mostrar que ocorreu um erro
                $this->mobileManager->setMessage("Ocorreu um erro ao realizar a solicitação. \n 
                Por favor tente novamente mais tarde", 0);
                $this->redirect()->toRoute('application_mobile');
            }
        } catch (Exception $e) {
            $this->mobileManager->setMessage($e->getMessage(), 0);
            $this->redirect()->toRoute('home');
        }
    }

    public function getLogMessagesAction()
    {
        return new JsonModel([
            'error' => $this->mobileManager->getMessage()
        ]);
    }
}