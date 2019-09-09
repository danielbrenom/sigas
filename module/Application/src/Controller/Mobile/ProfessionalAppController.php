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
        //UtilsFile::printvardie($this->mobileRepository->getConselhos());
        return new ViewModel([
            'user' => $profInfo,
            'cons' => $this->mobileRepository->getConselhos()
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
        $normPrescricoes = [];
        $size = count($params['fMedic']);
        for ($i = 0; $i < $size; $i++) {
            $normPrescricoes[] = [
                "medicamento" => $params['fMedic'][$i],
                "dosagem" => $params['fDose'][$i],
                "posologia" => $params['fPoso'][$i],
            ];
        }
        $this->mobileRepository->savePrescriptions(["pacid" => $params['pacId'], "docid" => $this->authManager->getActiveUser()['user_id'], "presc" => $normPrescricoes]);
        UtilsFile::printvardie($params, $normPrescricoes);
    }

    public function getLogMessagesAction()
    {
        return new JsonModel([
            'error' => $this->mobileRepository->getMessage()
        ]);
    }
}