<?php


namespace Authentication\Controller;


use Application\Controller\Repository\MobileRepository;
use Application\Debug\UtilsFile;
use Exception;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Result;
use Zend\Crypt\Utils;
use Zend\Json\Json;
use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\EntityManager;
use Authentication\Service\AuthenticationManager;
use Authentication\Service\UserManager;
use Zend\Uri\Uri;
use Zend\Validator\EmailAddress;
use Zend\Validator\Hostname;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class AuthenticationController extends AbstractActionController
{
    /**
     * Repository Manager.
     * @var MobileRepository
     */
    private $mobileRepository;

    /**
     * Auth manager.
     * @var AuthenticationManager
     */
    private $authManager;
    /**
     * User manager.
     * @var UserManager
     */
    private $userManager;

    /**
     * AuthenticationController constructor.
     * @param AuthenticationManager $authManager
     * @param UserManager $userManager
     */
    public function __construct(AuthenticationManager $authManager, UserManager $userManager, MobileRepository $mobileRepository)
    {
        $this->authManager = $authManager;
        $this->userManager = $userManager;
        $this->mobileRepository = $mobileRepository;
    }

    public function userStateAction()
    {
        return new JsonModel([
            "state" => $this->authManager->userState()
        ]);
    }

    public function loginAction()
    {
        try {
            $redirectUrl = $this->params()->fromQuery('r', '');
            if (strlen($redirectUrl) > 2048) {
                throw new Exception("Redirecionamento inválido");
            }
            $error = '';
            if ($this->getRequest()->isPost()) {
                $data = $this->params()->fromPost();
                //UtilsFile::printvardie($data);
                //$this->userManager->createAdminUserIfNotExists();
                $result = $this->authManager->login($data['fEmail'], $data['fPass']);
                if ($result->getCode() === Result::SUCCESS) {
                    $redirectUrl = $this->params()->fromPost('redirect_url', '');
                    if (!empty($redirectUrl)) {
                        $uri = new Uri($redirectUrl);
                        if (!$uri->isValid() || $uri->getHost() !== null)
                            throw new Exception('Incorrect redirect URL: ' . $redirectUrl);
                    }
                    if (empty($redirectUrl)) {
                        return $this->redirect()->toRoute('home');
                    }
                    $this->redirect()->toUrl($redirectUrl);
                } else {
                    throw new Exception($result->getMessages()[0]);
                }
            }
        } catch (Exception $e) {
            $error = $e;
        }
        $this->mobileRepository->setMessage($error->getMessage(), $error->getCode());
        $this->redirect()->toRoute('home');
        return false;
    }

    public function logoutAction()
    {
        $this->authManager->logout();
        return $this->redirect()->toRoute('home');
    }

    public function singupAction()
    {
        try {
            if ($this->getRequest()->isPost()) {
                $emailValidator = new EmailAddress([
                    "allow" => Hostname::ALLOW_DNS | Hostname::ALLOW_IP | Hostname::ALLOW_LOCAL,
                    "mxCheck" => true,
                    'deepMxCheck' => true
                ]);
                $data = $this->params()->fromPost();
                //UtilsFile::printvardie($data);
                if (!$emailValidator->isValid($data['fEmail'])) {
                    throw new Exception('Email inválido');
                }
                $this->userManager->createUser($data['fEmail'], $data['fPass']);
                $this->mobileRepository->setMessage("Usuário criado com sucesso. \n Efetue login.", 1);
                $this->redirect()->toRoute('home');
            } else {
                $this->redirect()->toRoute('home');
            }
        } catch (Exception $e) {
            $this->mobileRepository->setMessage($e->getMessage(), $e->getCode());
            $this->redirect()->toRoute('home');
        }
        return $this->getResponse();
    }
}