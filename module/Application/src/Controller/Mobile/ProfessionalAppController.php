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
        //UtilsFile::printvardie($this->mobileRepository->getProceduresProfessional($this->authManager->getActiveUser()['user_id']));
        return new ViewModel([
            'user' => $profInfo,
            'cons' => $this->mobileRepository->getConselhos(),
            'proceds' => $this->mobileRepository->getProceduresProfessional($this->authManager->getActiveUser()['user_id'])
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
                $procPacInfo = "";
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
            if ($this->mobileRepository->updateProfissionalJobInfo($params, $userId)) {
                $this->mobileRepository->setMessage("Informações atualizadas, aguarde confirmação", 1);
                $this->redirect()->toRoute('application_mobile_prof');
            } else {
                $this->mobileRepository->setMessage("Ocorreu um erro, tente novamente mais tarde.", 0);
                $this->redirect()->toRoute('application_mobile_prof');
            }
        } else {
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
        }
        return new JsonModel($response);
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
                            'esp_id' => $this->mobileRepository->getProfissionalInfo($thisProfId, true)[0]['esp_id'],
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
                        if ($this->mobileRepository->confirmAppointment($params)) {
                            $response = [
                                'code' => 1,
                                'message' => 'Solicitação confirmada'
                            ];
                        }else{
                            $response = [
                                'code' => 0,
                                'message' => 'Erro ao confirmar solicitação, tente novamente mais tarde.'
                            ];
                        }
                        break;
                    case 'cancel':
                        if($this->mobileRepository->cancelAppointment($params)){
                            $response = [
                                'code' => 1,
                                'message' => 'Solicitação cancelada'
                            ];
                        }else{
                            $response = [
                                'code' => 0,
                                'message' => 'Erro ao cancelar solicitação, tente novamente mais tarde.'
                            ];
                        }
                        break;
                    default:
                        throw new Exception("Requisição inválida", 0);
                        break;
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