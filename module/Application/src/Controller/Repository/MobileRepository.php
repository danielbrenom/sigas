<?php


namespace Application\Controller\Repository;


use Application\Debug\UtilsFile;
use Application\Entity\Seg\User;
use Application\Entity\Sis\UserAppointment;
use Application\Entity\Sis\UserEspeciality;
use Application\Entity\Sis\UserHistoric;
use Application\Entity\Sis\UserHistoricType;
use Application\Entity\Sis\UserInfoPessoal;
use Doctrine\ORM\EntityManager;
use Exception;

class MobileRepository
{
    protected $entityManager;

    public function __construct(EntityManager $em)
    {
        $this->entityManager = $em;
    }

    //Usuario

    public function getUserInformation($user_id)
    {
        return $this->entityManager->getRepository(User::class)
            ->createQueryBuilder('u')
            ->addSelect('info')
            ->leftJoin('u.user_information', 'info')
            ->where('u.id = :sId')
            ->setParameter('sId', $user_id)
            ->getQuery()->getResult(2);
    }

    public function getUserHistoric($user_id){

    }

    public function updateUserInfo($info, $user_id){
        UtilsFile::printvar($info);
        $userData = $this->entityManager->getRepository(UserInfoPessoal::class)->findBy(["id" => $user_id]);
        UtilsFile::printvar($userData);
        $userInfo = new UserInfoPessoal();
        $userInfo->setUserName($info['fName'] != "" ? $info['fName'] : " ");
        $userInfo->setUserCpf($info['fCpf'] != "" ? $info['fCpf'] : " ");
        $userInfo->setUserRg($info['fRg'] != "" ? $info['fRg'] : " ");
        $userInfo->setUserHealthcare($info['fPlano'] != "" ? $info['fPlano'] : " ");
        $userInfo->setUserCttPhone($info['fTelCel'] != "" ? $info['fTelCel'] : " ");
        $userInfo->setUserCttRes($info['fTelRes'] != "" ? $info['fTelRes'] : " ");
        UtilsFile::printvardie($userInfo);
    }

    //Profissional

    public function getSchedule($params)
    {
        return $this->entityManager->getRepository(UserAppointment::class)
            ->createQueryBuilder('a')
            ->where('a.id_user_ps = :sId')
            ->andWhere('a.solicited_for between :sIni and :sFim')
            ->setParameter("sIni", explode("T", $params['start'])[0])
            ->setParameter("sFim", explode("T", $params['end'])[0])
            ->setParameter("sId", $params['id_professional'])
            ->getQuery()->getResult(2);
    }

    public function getAppointmentDescription($appoint_id)
    {
        return $this->entityManager->getRepository(UserHistoricType::class)->find($appoint_id)->getHistoricTypeDescription();
    }

    public function getEspecialidade(){
        return $this->entityManager->getRepository(UserEspeciality::class)
            ->createQueryBuilder('e')
            ->where('e.id != 1')
            ->getQuery()->getResult(2);
    }

    public function getProfissinais($esp_id){
        return $this->entityManager->getRepository(User::class)
            ->createQueryBuilder('u')
            ->addSelect('info')
            ->leftJoin('u.user_information', 'info')
            ->where('info.user_especiality = :sId')
            ->setParameter('sId', $esp_id)
            ->getQuery()->getResult(2);
    }

    public function getProfissionalInfo($prof_id){
        return $this->entityManager->getRepository(User::class)
            ->createQueryBuilder('u')
            ->addSelect('info')
            ->addSelect('esp')
            ->leftJoin('u.user_information', 'info')
            ->leftJoin('info.user_especiality', 'esp')
            ->where('u.id = :sId')
            ->setParameter('sId', $prof_id)
            ->getQuery()->getResult(3);
    }

    //Operações no banco

    public function saveAppointment($params){
        try {
            $prof = $this->entityManager->getRepository(UserInfoPessoal::class)->createQueryBuilder('u')->addSelect('esp')->leftJoin('u.user_especiality', 'esp')->where('u.id = :sId')->setParameter('sId', $params['prof_req'])->getQuery()->getResult(3)[0];
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
            $userHistoric->setIdTypeRegistry(1);
            $userHistoric->setIdAppointmentEntry($userAppoint);
            $this->entityManager->persist($userHistoric);
            $this->entityManager->flush();
            $this->entityManager->commit();
            return true;
        } catch (Exception $e) {
            $this->entityManager->rollback();
            return false;
            //UtilsFile::printvardie($e->getMessage());
        }
    }
}