<?php


namespace Application\Controller\Mobile;


use Application\Repository\MobileRepository;
use Application\Debug\UtilsFile;
use Authentication\Service\AuthenticationManager;
use DateTime;
use DateTimeZone;
use Exception;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class ProfessionalAppController extends AbstractActionController
{

    protected $mobileRepository;
    protected $authManager;

    public function __construct(MobileRepository $mobileRepository, AuthenticationManager $authenticationManager)
    {
        $this->mobileRepository = $mobileRepository;
        $this->authManager = $authenticationManager;
    }

    public function homeAction()
    {
        $profInfo = $this->mobileRepository->getProfissionalInfo($this->authManager->getActiveUser()['user_id'], true)[0];
        //UtilsFile::printvardie($profInfo);
        return new ViewModel([
            'user' => $profInfo
        ]);
    }

    public function getScheduleAction()
    {
        $params = $this->getRequest()->getQuery()->toArray();
        $params['id_professional'] = $this->authManager->getActiveUser()['user_id'];
        $resp = [];
        try {
            $resultados = $this->mobileRepository->getSchedule($params, true);
            foreach ($resultados as $appointment) {
                $data = new DateTime($appointment['a_solicited_for'], new DateTimeZone('America/Belem'));
                $name = explode(' ', $appointment['user_name']);
                $resp[] = [
                    'title' => "{$this->mobileRepository->getAppointmentDescription($appointment['a_id_procedure'])} de {$name[0]}",
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

    public function getPacientesAction()
    {
        $params = $this->params()->fromQuery();
        $response = [];
        switch ($params['mode']) {
            case 'list':
                $response = $this->mobileRepository->getUsersAtendidosProfessional($this->authManager->getActiveUser()['user_id']);
                break;
            case 'details':
                break;
            default:
                break;
        }
        return new JsonModel($response);
    }

    public function getLogMessagesAction()
    {
        return new JsonModel([
            'error' => $this->mobileRepository->getMessage()
        ]);
    }
}