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
use Application\Entity\Sis\UserHistoricType;
use Application\Entity\Sis\UserInfoPessoal;
use Authentication\Service\AuthenticationManager;
use DateInterval;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManager;
use Exception;
use Zend\Mvc\Controller\AbstractActionController;
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
            "userstate" => $this->authManager->userState()
        ]);
    }

    public function getProfissionalInfoAction()
    {
        $params = $this->params()->fromQuery();
        $view = new ViewModel([
            "prof" => $this->mobileManager->getProfissionalInfo($params['id_user'])
        ]);
        $view->setTerminal(true);
        return $view;
    }

    public function getProfissionaisAction()
    {
        $params = $this->getRequest()->getQuery()->toArray();
        $view = new ViewModel([
            "profissionais" => $this->mobileManager->getProfissinais($params['esp'])
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
                $data = (new DateTime($appointment['solicited_for'], new DateTimeZone("America/Belem")))->format('Y-m-d');
                $resp[] = [
                    "title" => $this->mobileManager->getAppointmentDescription($appointment['id_procedure']),
                    "start" => $data,
                    "end" => $data
                ];
            }
        } catch (Exception $e) {
            $resp = $e->getMessage();
        }
        return new JsonModel(
            $resp
        );
    }

    public function getUserHistoricAction()
    {
        $params = $this->params()->fromQuery();
        $this->mobileManager->getUserHistoric($this->authManager->getActiveUser()['user_id']);
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
            $this->mobileManager->updateUserInfo($this->params()->fromPost(), $this->mobileManager->getUserInformation($activeUser['user_id']));
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
        try {
            if ($this->mobileManager->saveAppointment($vData)) {
                $this->redirect()->toRoute('application_mobile');
            } else {
                //Mostrar que ocorreu um erro
                $this->redirect()->toRoute('application_mobile');
            }
        } catch (Exception $e) {
            UtilsFile::printvardie($e->getMessage());
        }
    }

}