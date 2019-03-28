<?php
/**
 * Created by PhpStorm.
 * User: 400005
 * Date: 13/03/2019
 * Time: 09:24
 */

namespace Application\Controller;


use Application\Entity\Sis\User;
use Application\Entity\Sis\UserEspeciality;
use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class MobileController extends AbstractActionController
{

    /** @var $entityManager EntityManager */
    protected $entityManager;

    /**
     * MobileController constructor.
     */
    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function homeAction()
    {
        return new ViewModel();
    }

    public function getProfissionalInfoAction()
    {
        $params = $this->getRequest()->getQuery()->toArray();
        $prof = $this->entityManager->getRepository(User::class)
            ->createQueryBuilder('u')
            ->addSelect('info')
            ->addSelect('esp')
            ->leftJoin('u.user_information', 'info')
            ->leftJoin('u.user_especiality', 'esp')
            ->where('u.id = :sId')
            ->setParameter('sId', $params['id_user'])
            ->getQuery()->getResult(2);
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
            ->where('u.id_especialidade = :sId')
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

}