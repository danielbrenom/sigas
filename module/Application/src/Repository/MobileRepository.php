<?php


namespace Application\Repository;


use Application\Debug\UtilsFile;
use Application\Entity\Seg\User;
use Application\Entity\Sis\Procedures;
use Application\Entity\Sis\ProfessionalConselhos;
use Application\Entity\Sis\ProfessionalInfo;
use Application\Entity\Sis\UserAppointment;
use Application\Entity\Sis\UserEspeciality;
use Application\Entity\Sis\UserExams;
use Application\Entity\Sis\UserHealthcare;
use Application\Entity\Sis\UserHistoric;
use Application\Entity\Sis\UserHistoricInformation;
use Application\Entity\Sis\UserHistoricType;
use Application\Entity\Sis\UserInfoPessoal;
use Application\Entity\Sis\UserPrescription;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Exception;
use Zend\Session\Container;
use Zend\Session\SessionManager;
use Application\Entity\Sis\ProfessionalProcedures;

class MobileRepository
{
    protected $entityManager;
    /** @var $sessionManager SessionManager */
    protected $sessionManager;
    protected $sessionContainer;

    public function __construct(EntityManager $em, SessionManager $sessionManager)
    {
        $this->entityManager = $em;
        $this->sessionManager = $sessionManager;
        $this->sessionContainer = new Container('MessageContainer', $sessionManager);
    }

    //Usuario

    public function getUserInformation($user_id, $mode = 2)
    {
        return $this->entityManager->getRepository(User::class)
            ->createQueryBuilder('u')
            ->addSelect(['info', 'uhc.desc_healthcare'])
            ->leftJoin('u.user_information', 'info')
            ->leftJoin(UserHealthcare::class, 'uhc', 'WITH',
                'uhc.id = info.user_healthcare')
            ->where('u.id = :sId')
            ->setParameter('sId', $user_id)
            ->getQuery()->getResult($mode);
    }

    public function getUserHistoric($user_id, $type)
    {

        try {
            $results = [];
            if ((int)$type === 1) {
                $results = $this->entityManager->getRepository(UserHistoric::class)
                    ->createQueryBuilder('h')
                    ->select(['ap.solicited_for', 'ap.confirmed_for'])
                    ->addSelect('proc.procedure_description')
                    ->addSelect('ip.user_name as prof_name')
                    ->addSelect('e.desc_especialidade')
                    ->leftJoin(UserAppointment::class, 'ap',
                        'WITH', 'h.id_appointment_entry = ap.id')
                    ->leftJoin(Procedures::class, 'proc', 'WITH',
                        'proc.id = ap.id_procedure')
                    ->leftJoin(UserInfoPessoal::class, 'ip', 'WITH',
                        'ip.id = ap.id_user_ps')
                    ->leftJoin(UserEspeciality::class, 'e', 'WITH',
                        'e.id = ap.id_especiality')
                    ->where('h.historic_type = :sType and h.user_id = :sId')
                    ->setParameter('sType', $type)
                    ->setParameter('sId', $user_id)
                    ->getQuery()->getResult(3);
            }
            if ((int)$type === 2) {
                $results = $this->entityManager->getRepository(UserHistoric::class)
                    ->createQueryBuilder('h')
                    ->addSelect('ap')
                    ->leftJoin(UserAppointment::class, 'ap',
                        'WITH', 'h.id_appointment_entry = ap.id')
                    ->where('h.historic_type = :sType and h.user_id = :sId')
                    ->setParameter('sType', $type)
                    ->setParameter('sId', $user_id)
                    ->getQuery()->getResult(3);
            }
            return $results;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function updateUserInfo($info, $user_id)
    {
        try {
            //UtilsFile::printvar($info);
            /** @var UserInfoPessoal $userData */
            $this->entityManager->beginTransaction();
            $userData = $this->entityManager->getRepository(UserInfoPessoal::class)->find($user_id);
            //UtilsFile::printvar($userData->toArray());
            $userData->setUserName($info['fName'] != "" ? $info['fName'] : $userData->getUserName());
            $userData->setUserAddr($info['fEnd'] != "" ? $info['fEnd'] : $userData->getUserAddr());
            $userData->setUserCpf($info['fCpf'] != "" ? $info['fCpf'] : $userData->getUserCpf());
            $userData->setUserRg($info['fRg'] != "" ? $info['fRg'] : $userData->getUserRg());
            $userData->setUserHealthcare($info['fPlano'] != "" ? $info['fPlano'] : $userData->getUserHealthcare());
            $userData->setUserCttPhone($info['fTelCel'] != "" ? $info['fTelCel'] : $userData->getUserCttPhone());
            $userData->setUserCttRes($info['fTelRes'] != "" ? $info['fTelRes'] : $userData->getUserCttRes());
            $this->entityManager->flush();
            $this->entityManager->commit();
            $this->setMessage('Informações salvas com sucesso', 1);
            //UtilsFile::printvardie($userData->toArray());
        } catch (Exception $e) {
            $this->entityManager->rollback();
            $this->setMessage("Um erro ocorreu ao alterar as informações. \n 
            Tente novamente mais tarde.", 2);
            //UtilsFile::printvardie($e->getMessage(),$e->getTraceAsString());
        }
    }

    public function getUsersAtendidosProfessional($prof_id)
    {
        return $this->entityManager->getRepository(UserAppointment::class)
            ->createQueryBuilder('ua')
            ->distinct()
            ->select(['ui.user_name', 'ui.id', 'uhc.desc_healthcare'])
            ->leftJoin(UserHistoric::class, 'uh', 'WITH',
                'uh.id_appointment_entry = ua.id')
            ->leftJoin(UserInfoPessoal::class, 'ui', 'WITH',
                'ui.id = uh.user_id')
            ->leftJoin(UserHealthcare::class, 'uhc', 'WITH',
                'uhc.id = ui.user_healthcare')
            ->where('uh.historic_type = 1 and ua.id_user_ps = :sId')
            ->setParameter('sId', $prof_id)
            ->getQuery()->getResult(3);
    }

    //Profissional

    public function getSchedule($params, $for_prof = false)
    {
        $sql = $this->entityManager->getRepository(UserAppointment::class)
            ->createQueryBuilder('a')
            ->where('a.id_user_ps = :sId')
            ->andWhere('a.solicited_for between :sIni and :sFim')
            ->setParameter("sIni", explode("T", $params['start'])[0])
            ->setParameter("sFim", explode("T", $params['end'])[0])
            ->setParameter("sId", $params['id_professional']);
        if ($for_prof) {
            $sql->leftJoin(UserHistoric::class, 'uh', 'WITH', 'a.id = uh.id_appointment_entry')
                ->addSelect('pac_info.user_name')->leftJoin(UserInfoPessoal::class, 'pac_info', 'WITH', 'pac_info.id = uh.user_id')
                ->andWhere("uh.historic_type = 1");
        }
        return $sql->getQuery()->getResult(3);
    }

    public function getSolicitacoes($params)
    {
        return $this->entityManager->getRepository(UserAppointment::class)->createQueryBuilder('a')
            ->leftJoin(UserHistoric::class, 'uh', 'WITH', 'a.id = uh.id_appointment_entry')
            ->addSelect('pac_info.user_name')
            ->addSelect('proc.procedure_description')
            ->leftJoin(UserInfoPessoal::class, 'pac_info', 'WITH', 'pac_info.id = uh.user_id')
            ->leftJoin(Procedures::class, 'proc', 'WITH', 'proc.id = a.id_procedure')
            ->where('a.id_user_ps = :sId and a.confirmed_for is null and a.id_status not in (2,4)')
            ->setParameter('sId', $params['id_professional'])
            ->getQuery()->getResult(3);
    }

    public function getAppointmentDescription($appoint_id)
    {
        return $this->entityManager->getRepository(Procedures::class)->find($appoint_id)->getProcedureDescription();
    }

    public function getProceduresProfessional($id_prof)
    {
        return $this->entityManager->getRepository(ProfessionalProcedures::class)
            ->createQueryBuilder('p')
            ->addSelect('pd.procedure_description')
            ->leftJoin(Procedures::class, 'pd', 'WITH', 'p.id_procedure = pd.id')
            ->where('p.id_professional = :sId')
            ->setParameter('sId', $id_prof)
            ->getQuery()->getResult(3);
    }

    public function getEspecialidade()
    {
        return $this->entityManager->getRepository(UserEspeciality::class)
            ->createQueryBuilder('e')
            ->where('e.id != 1')
            ->getQuery()->getResult(2);
    }

    public function getProfissinais($esp_id)
    {
        return $this->entityManager->getRepository(User::class)
            ->createQueryBuilder('u')
            ->addSelect('info')
            ->leftJoin('u.user_information', 'info')
            ->leftJoin(ProfessionalInfo::class, 'pi', 'WITH', 'pi.id_user = u.id')
            ->where('pi.id_especiality = :sId')
            ->setParameter('sId', $esp_id)
            ->getQuery()->getResult(2);
    }

    public function getProfissionalInfo($prof_id, $completeInfo = false)
    {
        $sql = $this->entityManager->getRepository(User::class)
            ->createQueryBuilder('u')
            ->addSelect('info')
            ->leftJoin('u.user_information', 'info')
            ->where('u.id = :sId')
            ->setParameter('sId', $prof_id);
        if ($completeInfo) {
            $sql->addSelect('pi')->leftJoin(ProfessionalInfo::class, 'pi', 'WITH', 'pi.id_user = u.id');
            $sql->addSelect('pc')->leftJoin(ProfessionalConselhos::class, 'pc', 'WITH', 'pi.cons_name = pc.id');
            $tempSql = $sql->getQuery()->getResult(3);
            if ($tempSql[0]['pi_confirmed_in'] !== null) {
                $sql->addSelect('esp')->leftJoin(UserEspeciality::class, 'esp', 'WITH', 'esp.id = pi.id_especiality');
            }
        }
        return $sql->getQuery()->getResult(3);
    }

    public function updateProfissionalJobInfo($info, $prof_id)
    {
        try {
            $this->entityManager->beginTransaction();
            $date = new \DateTime('now', new \DateTimeZone('America/Belem'));
            /** @var ProfessionalInfo $profInfo */
            $profInfo = $this->entityManager->getRepository(ProfessionalInfo::class)->find($prof_id);
            $profInfo->setConsName($info['fCons']);
            $profInfo->setEspecialitySolicited($info['fEspSoc']);
            $profInfo->setConsRegistry($info['fNumIns']);
            $profInfo->setConfirmedIn(null);
            $profInfo->setSolicitedIn($date->format('Y-m-d H:i:s'));
            /** @var UserInfoPessoal $profInfoPes */
            $profInfoPes = $this->entityManager->getRepository(UserInfoPessoal::class)->find($prof_id);
            $profInfoPes->setUserCttPhone($info['fTelCel']);
            $profInfoPes->setUserCttRes($info['fTelRes']);
            //UtilsFile::printvardie($profInfoPes);
            $this->entityManager->flush();
            $this->entityManager->commit();
            return true;
        } catch (Exception $e) {
            $this->entityManager->rollback();
            UtilsFile::printvardie($e->getMessage());
            return false;
        }
    }

    public function updateProfissionalPesInfo($info, $prof_id)
    {
    }

    //Buscas gerais

    public function getProceduresAvailableForUser($pac_id)
    {
        return $this->entityManager->getRepository(UserHistoric::class)
            ->createQueryBuilder('uh')
            ->distinct()
            ->select(['uh.historic_type', 'uht.historic_type_description'])
            ->leftJoin(UserHistoricType::class, 'uht', 'WITH',
                'uh.historic_type = uht.id')
            ->where('uh.user_id = :sId')
            ->setParameter('sId', $pac_id)
            ->getQuery()->getResult(3);
    }

    public function getConselhos()
    {
        return $this->entityManager->getRepository(ProfessionalConselhos::class)
            ->createQueryBuilder('c')
            ->getQuery()->getResult(3);
    }

    //Operações no banco
    public function saveAppointment($params)
    {
        try {
            $prof = $this->getProfissionalInfo($params['prof_req'], true)[0];
            //UtilsFile::printvardie($params);
            $this->entityManager->beginTransaction();
            //Primeiro deve criar o appointment para depois criar o registro no historico
            $userAppoint = new UserAppointment();
            $userAppoint->setIdUserPs($params['prof_req']);
            $userAppoint->setCreatedOn(date("Y-m-d H:i:s"));
            $userAppoint->setSolicitedFor(date("Y-m-d H:i:s", strtotime("{$params['datareq']} {$params['horareq']}")));
            $userAppoint->setIdEspeciality($prof['esp_id']);
            $userAppoint->setIdProcedure($params['proc']);
            //UtilsFile::printvardie($userAppoint);
            $this->entityManager->persist($userAppoint);
            $this->entityManager->flush();
            $userHistoric = new UserHistoric();
            $userHistoric->setUserId($this->entityManager->getRepository(User::class)->find($params['user_req']));
            $userHistoric->setHistoricType($params['proc']);
            $userHistoric->setIdReginformation(null);
            $userHistoric->setIdAppointmentEntry($userAppoint);
            $this->entityManager->persist($userHistoric);
            $this->entityManager->flush();
            if (isset($params['info'])) {
                $infoHistoric = new UserHistoricInformation();
                $infoHistoric->setIdHistoricReg($userHistoric->getIdHistoric());
                $infoHistoric->setHistoricInformation($params['titulo'] . ' - ' . $params['info']);
                $this->entityManager->persist($infoHistoric);
                $this->entityManager->flush();
            }
            $this->entityManager->commit();
            return true;
        } catch (Exception $e) {
            //UtilsFile::printvardie($e->getMessage());
            $this->entityManager->rollback();
            return false;
        }
    }

    public function confirmAppointment($params)
    {
        try {
            $this->entityManager->beginTransaction();
            /** @var UserAppointment $app */
            $app = $this->entityManager->getRepository(UserAppointment::class)->find($params['ap_id']);
            $app->setConfirmedFor($app->getSolicitedFor());
            $app->setIdStatus(2);
            $this->entityManager->flush();
            $this->entityManager->commit();
            return true;
        } catch (Exception $e) {
            $this->entityManager->rollback();
            return false;
        }
    }

    public function cancelAppointment($params){
        try {
            $this->entityManager->beginTransaction();
            /** @var UserAppointment $app */
            $app = $this->entityManager->getRepository(UserAppointment::class)->find($params['ap_id']);
            $app->setIdStatus(4);
            $this->entityManager->flush();
            $this->entityManager->commit();
            return true;
        } catch (Exception $e) {
            $this->entityManager->rollback();
            return false;
        }
    }

    public function savePrescriptions($params)
    {
        try {
            $this->entityManager->beginTransaction();
            foreach ($params['presc'] as $prescicao) {
                $prescription = new UserPrescription();
                $prescription->setPrescMedicamento($prescicao['medicamento']);
                $prescription->setPrescDosagem($prescicao['dosagem']);
                $prescription->setPrescPosologia($prescicao['posologia']);
                $prescription->setProfCadastro($params['docid']);
                $prescription->setDtCadastro((new \DateTime('now', new \DateTimeZone("America/Belem")))->format("Y-m-d H:i:s"));
                $this->entityManager->persist($prescription);
                $this->entityManager->flush();
                $historic = new UserHistoric();
                $historic->setUserId($this->entityManager->getRepository(User::class)->find($params['pacid']));
                $historic->setHistoricType(4);
                $historic->setIdGenericEntry($prescription->getId());
                $this->entityManager->persist($historic);
                $this->entityManager->flush();
            }
            $this->entityManager->commit();
            return true;
        } catch (Exception $e) {
            $this->entityManager->rollback();
            return false;
        }
    }

    public function saveExams($params)
    {
        try {
            $this->entityManager->beginTransaction();
            $exam = new UserExams();
            $exam->setExamName($params['fExam']);
            $exam->setExamCodigo($params['fCodigo']);
            $exam->setExamNotes($params['fDesc']);
            $this->entityManager->persist($exam);
            $this->entityManager->flush();
            $historic = new UserHistoric();
            $historic->setUserId($this->entityManager->getRepository(User::class)->find($params['pacId']));
            $historic->setHistoricType(2);
            $historic->setIdGenericEntry($exam->getId());
            $this->entityManager->persist($historic);
            $this->entityManager->flush();
            $this->entityManager->commit();
            return true;
        } catch (Exception $e) {
            $this->entityManager->rollback();
            return false;
            UtilsFile::printvardie($e->getMessage());
        }
    }

    //Operações na sessão
    public function setMessage($message, $code)
    {
        if (isset($this->sessionContainer->message)) {
            unset($this->sessionContainer->message);
        }
        $this->sessionContainer->message = [
            "code" => $code,
            "message" => $message
        ];
    }

    public function getMessage()
    {
        if (isset($this->sessionContainer->message)) {
            $message = $this->sessionContainer->message;
            unset($this->sessionContainer->message);
            return $message;
        }
        return false;
    }
}