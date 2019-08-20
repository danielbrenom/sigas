<?php
/**
 * Created by PhpStorm.
 * User: 400005
 * Date: 13/03/2019
 * Time: 09:24
 */

namespace Application\Controller;


use Application\Debug\UtilsFile;
use Application\Entity\Seg\User;
use Application\Entity\Sis\UserAppointment;
use Application\Entity\Sis\UserEspeciality;
use Application\Entity\Sis\UserHistoric;
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

    /** @var $entityManager EntityManager */
    protected $entityManager;
    /** @var $authManager AuthenticationManager */
    protected $authManager;

    /**
     * MobileController constructor.
     * @param $entityManager
     * @param $authManager
     */
    public function __construct($entityManager, $authManager)
    {
        $this->entityManager = $entityManager;
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
        $params = $this->getRequest()->getQuery()->toArray();
        $prof = $this->entityManager->getRepository(User::class)
            ->createQueryBuilder('u')
            ->addSelect('info')
            ->addSelect('esp')
            ->leftJoin('u.user_information', 'info')
            ->leftJoin('info.user_especiality', 'esp')
            ->where('u.id = :sId')
            ->setParameter('sId', $params['id_user'])
            ->getQuery()->getResult(3);
        $view = new ViewModel([
            "prof" => $prof
        ]);
        $view->setTerminal(true);
        return $view;
    }

    public function getProfissionaisAction()
    {
        $params = $this->getRequest()->getQuery()->toArray();
        $prof = $this->entityManager->getRepository(User::class)
            ->createQueryBuilder('u')
            ->addSelect('info')
            ->leftJoin('u.user_information', 'info')
            ->where('info.user_especiality = :sId')
            ->setParameter('sId', $params['esp'])
            ->getQuery()->getResult(2);
        $view = new ViewModel([
            "profissionais" => $prof
        ]);
        $view->setTerminal(true);
        return $view;
    }

    public function getEspecialidadesAction()
    {
        $esp = $this->entityManager->getRepository(UserEspeciality::class)
            ->createQueryBuilder('e')
            ->where('e.id != 1')
            ->getQuery()->getResult(2);
        return new JsonModel([
            $esp
        ]);
    }

    public function getScheduleAction()
    {
        $params = $this->getRequest()->getQuery()->toArray();
        $params['id_professional'] = 2;
        try {
            $resultados = $this->entityManager->getRepository(UserAppointment::class)
                ->createQueryBuilder('a')
                ->where('a.id_user_ps = :sId')
                ->setParameter("sId", $params['id_professional'])
                ->getQuery()->getResult(2);
            $resp = [];
            foreach ($resultados as $appointment) {
                $data = (new DateTime($appointment['solicited_for'], new DateTimeZone("America/Belem")))->format('Y-m-d');
                $resp[] = [
                    "title" => "A",
                    "start" => $data,
                    "end" => $data
                ];
            }
        } catch (Exception $e) {
            $resp = $e->getMessage();
        }
        return new JsonModel([
            $resp[0]
        ]);
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
        $userInfo = $this->entityManager->getRepository(User::class)
            ->createQueryBuilder('u')
            ->addSelect('info')
            ->leftJoin('u.user_information', 'info')
            ->where('u.id = :sId')
            ->setParameter('sId', $activeUser['user_id'])
            ->getQuery()->getResult(2);
        $view = new ViewModel([
            'user' => $userInfo[0]
        ]);
        $view->setTerminal(true);
        return $view;
    }

    public function saveAppointAction(){
        $params = $this->params()->fromPost();
        $vData = [
            "datareq" => UtilsFile::formataDataToBdSemHora($params['fDataReq']),
            "horareq" => $params['fHoraReq'],
            "proc" => $params['fProcdReq'],
            "user_req" => $this->authManager->getActiveUser()['user_id'],
            "prof_req" => $params['fIdProf']
        ];
        UtilsFile::printvar($params, $vData);
        try {
            $this->entityManager->beginTransaction();
            //Primeiro deve criar o appointment para depois criar o registro no historico
            $userAppoint = new UserAppointment();
            $userAppoint->setIdUserPs($vData['prof_req']);
            $userAppoint->setCreatedOn(date("Y-m-d H:i:s"));
            $userAppoint->setSolicitedFor(date("Y-m-d H:i:s", strtotime("{$vData['datareq']} {$vData['horareq']}")));
            //$userAppoint->setIdEspeciality();
//            $this->entityManager->getRepository(UserInfoPessoal::class)->createQueryBuilder('u')->addSelect('esp')->leftJoin('u.user_especiality', 'esp')->where('u.id = :sId')->setParameter('sId',$vData['prof_req'])->getQuery()->getResult(3)[0]
            UtilsFile::printvardie($userAppoint, $this->entityManager->getRepository(UserInfoPessoal::class)->findOneBy(['id'=>$vData['prof_req']])
               );
            $userHistoric = new UserHistoric();
            $userHistoric->setUserId($vData['user_req']);
            $userHistoric->setIdTypeRegistry(1);
        }catch (Exception $e){
            $this->entityManager->rollback();
            UtilsFile::printvardie($e->getMessage());
        }
    }

}