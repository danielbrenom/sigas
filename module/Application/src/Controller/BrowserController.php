<?php
/**
 * Created by PhpStorm.
 * User: 400005
 * Date: 13/03/2019
 * Time: 09:26
 */

namespace Application\Controller;


use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class BrowserController extends AbstractActionController
{

    public function homeAction(){
        return new ViewModel();
    }

    public function historyAction(){

    }

    public function profileAction(){

    }
}