<?php


namespace Application\Controller\Mobile;


use Application\Repository\MobileRepository;
use Application\Debug\UtilsFile;
use Authentication\Service\AuthenticationManager;
use DateTime;
use DateTimeZone;
use Exception;
use Zend\Json\Json;
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
        $profInfo['pi_prof_addresses'] = Json::decode($profInfo['pi_prof_addresses']);
        //UtilsFile::printvardie($profInfo);
        return new ViewModel([
            'user' => $profInfo,
            'cons' => $this->mobileRepository->getConselhos(),
            'proceds' => $this->mobileRepository->getProceduresProfessional($this->authManager->getActiveUser()['user_id']),
            'attend' => $this->mobileRepository->getProfessionalAttendants(['idp' => $this->authManager->getActiveUser()['user_id']])
        ]);
    }

    public function getScheduleAction()
    {
        $params = $this->getRequest()->getQuery()->toArray();
        $params['id_professional'] = $this->authManager->getActiveUser()['user_id'];
        $resp = [];
        try {
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
//                            'color' => $appointment['a_confirmed_for'] == null ? "#d75126": "#a4d997"
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
            default:
                $response = [
                    'code' => 0,
                    'message' => 'Solicitação inválida'
                ];
                break;
        }
        return new JsonModel($response);
    }

    public function getProfileAction()
    {
        $userId = $this->authManager->getActiveUser()['user_id'];
        if ($this->getRequest()->isPost()) {
            $params = $this->params()->fromPost();
            if ($params['fOp'] == 'profp') {
                if ($this->mobileRepository->updateProfissionalJobInfo($params, $userId)) {
                    $this->mobileRepository->setMessage("Informações atualizadas, aguarde confirmação", 1);
                    $this->redirect()->toRoute('application_mobile_prof');
                } else {
                    $this->mobileRepository->setMessage("Ocorreu um erro, tente novamente mais tarde.", 0);
                    $this->redirect()->toRoute('application_mobile_prof');
                }
            } elseif ($params['fOp'] == 'pesp') {
                if ($this->mobileRepository->updateProfissionalPesInfo($params, $userId)) {
                    $this->mobileRepository->setMessage("Informações atualizadas, aguarde confirmação", 1);
                    $this->redirect()->toRoute('application_mobile_prof');
                } else {
                    $this->mobileRepository->setMessage("Ocorreu um erro, tente novamente mais tarde.", 0);
                    $this->redirect()->toRoute('application_mobile_prof');
                }
            }
            return $this->getResponse();
        }

        $params = $this->params()->fromQuery();
        switch ($params['type']) {
            case 'pes':
                break;
            case 'prof':
                $profInfo = $this->mobileRepository->getProfissionalInfo($userId, true)[0];
                $response = [
                    'info_user_addr' => $profInfo['info_user_addr'],
                    'info_user_ctt_phone' => $profInfo['info_user_ctt_phone'],
                    'info_user_ctt_res' => $profInfo['info_user_ctt_res'],
                    'pi_cons_name' => $profInfo['pi_cons_name'],
                    'pi_cons_registry' => $profInfo['pi_cons_registry'],
                    'pi_especiality_solicited' => $profInfo['pi_especiality_solicited'],
                ];
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

    public function attendantAction()
    {
        try {
            if ($this->getRequest()->isPost()) {
                $params = $this->params()->fromPost();
                $params['idp'] = $this->authManager->getActiveUser()['user_id'];
                if ($this->mobileRepository->saveProfessionalAttendants($params)) {
                    $this->mobileRepository->setMessage('Atendentes registrados.', 1);
                    $this->redirect()->toRoute('application_mobile_prof');
                    return $this->getResponse();
                }

                $this->mobileRepository->setMessage("Ocorreu um erro, tente novamente mais tarde", 0);
                $this->redirect()->toRoute('application_mobile_prof');
                return $this->getResponse();
            }
            if ($this->getRequest()->isGet()) {
                $params = $this->params()->fromQuery();
                $params['id_professional'] = $this->authManager->getActiveUser()['user_id'];
                $allAtt = $this->mobileRepository->getAttendants();
                foreach ($allAtt as $key => $att) {
                    $allAtt[$key]['is_att'] = $this->mobileRepository->isProfessionalAtendant(
                        ['pid' => $params['id_professional'],
                            'aid' => $att['id_attendant']]);
                }
                return new JsonModel($allAtt);
            }
            throw new Exception("Requisição inválida", 0);
        } catch (Exception $e) {
            return new JsonModel([$e->getMessage()]);
            $this->mobileRepository->setMessage($e->getMessage(), $e->getCode());
            $this->redirect()->toRoute('application_mobile_prof');
            return $this->getResponse();
        }
    }

    public function procedureAction()
    {
        try {
            if($this->getRequest()->isGet()){
                $params = $this->params()->fromQuery();
                $params['id_professional'] = $this->authManager->getActiveUser()['user_id'];
                $allProc = $this->mobileRepository->getProcedures();
                //UtilsFile::printvardie($allProc);
                foreach ($allProc as $key => $proc){
                    $allProc[$key]['is_proc'] = $this->mobileRepository->isProfessionalProcedures(
                        [
                            'idproc' => $proc['p_id'],
                            'idprof'=> $params['id_professional']
                        ]
                    );
                }
                return new JsonModel($allProc);
            }
            if($this->getRequest()->isPost()){
                $params = $this->params()->fromPost();
                $params['id_professional'] = $this->authManager->getActiveUser()['user_id'];
                if($this->mobileRepository->saveProfessionalProcedures($params)){
                    $this->mobileRepository->setMessage('Procedimentos registrados.', 1);
                    $this->redirect()->toRoute('application_mobile_prof');
                    return $this->getResponse();
                }
                $this->mobileRepository->setMessage('Ocorreu um erro, tente novamente mais tarde', 0);
                $this->redirect()->toRoute('application_mobile_prof');
                return $this->getResponse();
            }
            throw new Exception("Requisição inválida", 0);
        } catch (Exception $e) {
            return new JsonModel([$e->getMessage()]);
            $this->mobileRepository->setMessage($e->getMessage(), $e->getCode());
            return $this->redirect()->toRoute('application_mobile_prof');
        }
    }

    public function saveHistoricAction()
    {
        $params = $this->params()->fromPost();
        $thisProfId = $this->authManager->getActiveUser()['user_id'];
        switch ($params['op']) {
            case 'prescriptions':
                $normPrescricoes = [];
                $size = count($params['fMedic']);
                for ($i = 0; $i < $size; $i++) {
                    if ($params['fMedic'][$i] == "") continue;
                    $normPrescricoes[] = [
                        "medicamento" => $params['fMedic'][$i],
                        "dosagem" => $params['fDose'][$i],
                        "posologia" => $params['fPoso'][$i],
                    ];
                }
                if ($this->mobileRepository->savePrescriptions(["pacid" => $params['pacId'], "docid" => $thisProfId, "presc" => $normPrescricoes])) {
                    $this->mobileRepository->setMessage("Prescrição salva.", 1);
                } else {
                    $this->mobileRepository->setMessage("Ocorreu um erro, tente novamente mais tarde", 0);
                }
                break;
            case 'rx':
                $params['docid'] = $this->authManager->getActiveUser()['user_id'];
                if ($this->mobileRepository->saveExams($params)) {
                    $this->mobileRepository->setMessage("Exame salvo.", 1);
                } else {
                    $this->mobileRepository->setMessage("Ocorreu um erro, tente novamente mais tarde", 0);
                }
                break;
            case 'rem':
                //UtilsFile::printvardie($params);
                switch ($params['fType']) {
                    case 1:
                        if ($this->mobileRepository->saveAppointment([
                            "prof_req" => $thisProfId,
                            'user_req' => $params["pacId"],
                            'proc' => $params['fType'],
                            'datareq' => $params['fData'],
                            'horareq' => $params['fHora'],
                            'info' => $params['fDesc'],
                            'titulo' => $params['fDescProc']
                        ])) {
                            $this->mobileRepository->setMessage("Procedimento salvo.", 1);
                        } else {
                            $this->mobileRepository->setMessage("Ocorreu um erro, tente novamente mais tarde", 0);
                        }
                        break;
                }
                break;
            case 'notif':
                $params['idprof'] = $thisProfId;
                if ($this->mobileRepository->saveNotifis($params)) {
                    $this->mobileRepository->setMessage("Notificação salva. Os compromissos existentes no período foram cancelados.", 1);
                } else {
                    $this->mobileRepository->setMessage("Ocorreu um erro, tente novamente mais tarde", 0);
                }
                break;
            default:
                $this->redirect()->toRoute('home');
                break;
        }
        $this->redirect()->toRoute('application_mobile_prof');
    }

    public function handleSolicitacoesAction()
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