<?php
/**
 * Created by PhpStorm.
 * User: 400005
 * Date: 13/03/2019
 * Time: 09:24
 */

namespace Application\Controller;


use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class MobileController extends AbstractActionController
{
    public function homeAction(){
        return new ViewModel();
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