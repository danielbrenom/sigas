<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Zend\View\Renderer\PhpRenderer;

class IndexController extends AbstractActionController
{

    public function homeAction()
    {
        $view = new ViewModel();
        return $view;
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
