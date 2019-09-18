<?php


namespace Application\Controller\Mobile;


use Application\Debug\UtilsFile;
use Application\Repository\MobileRepository;
use Authentication\Service\AuthenticationManager;
use DateTime;
use DateTimeZone;
use Exception;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class AttendantAppController extends AbstractActionController
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
        //UtilsFile::printvardie($this->mobileRepository->getAttendantProfessionals(['ida' => $this->authManager->getActiveUser()['user_id']]));
        return new ViewModel([
            'user' => $profInfo,
            'attend' => $this->mobileRepository->getAttendantProfessionals(['ida' => $this->authManager->getActiveUser()['user_id']])
        ]);
    }

    public function pacientesAction(){
        $params = $this->params()->fromQuery();
        $response = [];
        switch ($params['mode']) {
            case 'list':
                $response = $this->mobileRepository->getUsersAtendidosProfessional($params['pid']);
                break;
            case 'details':
                $pacInfo = $this->mobileRepository->getUserInformation($params['pac_id'], 3)[0];
                $response['u_name'] = $pacInfo['info_user_name'];
                $response['u_cpf'] = $pacInfo['info_user_cpf'];
                $response['u_ctt_phone'] = $pacInfo['info_user_ctt_phone'];
                $response['u_ctt_res'] = $pacInfo['info_user_ctt_res'];
                $response['u_healthcare'] = $pacInfo['desc_healthcare'];
                $response['reg_types'] = $this->mobileRepository->getProceduresAvailableForUser($params['pac_id']);
                break;
            case 'procedure':
                $response = $this->mobileRepository->getUserHistoric($params['user'], $params['ptype']);
                break;
            case 'appt':
                $response = $this->mobileRepository->getPacientes();
                break;
            default:
                $response = [
                    'code' => 0,
                    'message' => 'Solicitação inválida'
                ];
                break;
        }
        return new JsonModel($response);
    }

    public function scheduleAction()
    {
        try {
            if ($this->getRequest()->isGet()) {
                $params = $this->params()->fromQuery();
                $params['id_professional'] = $params['pid'];
                $resp = [];
                switch ($params['type']) {
                    case 'schedule':
                        $resultados = $this->mobileRepository->getSchedule($params, true);
                        foreach ($resultados as $appointment) {
                            $data = new DateTime($appointment['a_confirmed_for'] == null ? $appointment['a_solicited_for'] : $appointment['a_confirmed_for'], new DateTimeZone('America/Belem'));
                            $name = explode(' ', $appointment['user_name']);
                            $resp[] = [
                                'title' => "{$this->mobileRepository->getAppointmentDescription($appointment['a_id_procedure'])} de {$name[0]}",
                                'start' => $data->format('Y-m-d') . 'T' . $data->format('H:i:s') . '-03:00',
                                'end' => $data->format('Y-m-d') . 'T' . $data->format('H:i:s') . '-03:00',
                                'classNames' => $appointment['a_confirmed_for'] == null ? "pending" : "confirmed",
                            ];
                        }
                        break;
                    case 'solics':
                        $resp = $this->mobileRepository->getSolicitacoes($params);
//                    UtilsFile::printvardie($solics);
                        break;
                    case 'notifs':
                        $resp = $this->mobileRepository->getNotificacoes($params);
//                    UtilsFile::printvardie($solics);
                        break;
                }
                return new JsonModel($resp);
//                return $this->response->setStatusCode(200);
            }
        } catch (Exception $e) {
            $this->response->setContent($e->getMessage());
        }
        return $this->response->setStatusCode(400);
    }

    public function solicitacoesAction()
    {
        try {
            if ($this->getRequest()->isPost()) {
                $params = $this->params()->fromPost();
                $response = [];
                switch ($params['mode']) {
                    case 'confirm':
                        $params['status'] = 2;
                        $result = $this->mobileRepository->handleAppointment($params);
                        break;
                    case 'cancel':
                        $params['status'] = 4;
                        $result = $this->mobileRepository->handleAppointment($params);
                        break;
                    case 'reschedule':
                        $params['status'] = 3;
                        $result = $this->mobileRepository->handleAppointment($params);
                        break;
                    default:
                        throw new Exception("Requisição inválida", 0);
                        break;
                }
                if ($result) {
                    $response = [
                        'code' => 1,
                        'message' => 'A alteração da solicitação foi salva.'
                    ];
                } else {
                    $response = [
                        'code' => 0,
                        'message' => 'Erro ao operar solicitação, tente novamente mais tarde.'
                    ];
                }
                if(isset($params['origin'])){
                    $this->mobileRepository->setMessage($response['message'], $response['code']);
                    return $this->redirect()->toRoute('application_mobile_attendant');
                }
                return new JsonModel($response);
            }

            $this->getResponse()->setStatusCode(404);
            throw new Exception("Requisição inválida", 0);
        } catch (Exception $e) {
            return new JsonModel([
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getLogMessagesAction()
    {
        return new JsonModel([
            'error' => $this->mobileRepository->getMessage()
            //'error' => ['code'=> 0, 'message' => "Registro salvo"]
        ]);
    }
}