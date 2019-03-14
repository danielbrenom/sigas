<?php
/**
 * Created by PhpStorm.
 * User: 400005
 * Date: 13/03/2019
 * Time: 09:24
 */

namespace Application\Controller;


use Application\Entity\UserEspeciality;
use Doctrine\ORM\EntityManager;
use Zend\Mvc\Controller\AbstractActionController;
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

    public function homeAction(){
        $esp = $this->entityManager->getRepository(UserEspeciality::class)
        ->createQueryBuilder('e')
        ->where('e.id != 0')
        ->getQuery()->getResult(2);
        return new ViewModel([
            "especialities" => $esp
        ]);
    }

    public function getProfissionaisAction()
    {
        $elements = [];
        for ($i = 0; $i <= 20; $i++){
            $elements[] = "A{$i}";
        }
        $view = new ViewModel(
            [
                "id" => $elements
            ]
        );
        $view->setTerminal(true);
        return $view;
    }

}