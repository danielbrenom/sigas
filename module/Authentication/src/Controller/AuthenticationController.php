<?php


namespace Authentication\Controller;


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
     * Entity manager.
     * @var EntityManager
     */
    private $entityManager;

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
     * @param EntityManager $entityManager
     * @param AuthenticationManager $authManager
     * @param UserManager $userManager
     */
    public function __construct(EntityManager $entityManager, AuthenticationManager $authManager, UserManager $userManager)
    {
        $this->entityManager = $entityManager;
        $this->authManager = $authManager;
        $this->userManager = $userManager;
    }

    public function userStateAction()
    {
        return new JsonModel([
            "state" => $this->authManager->userState()
        ]);
    }

    public function loginAction()
    {
        $redirectUrl = $this->params()->fromQuery('r', '');
        if (strlen($redirectUrl) > 2048) {
            throw new Exception("Redirecionamento inválido");
        }
        $isLoginError = false;
        try {
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
                    $isLoginError = true;
                    //UtilsFile::printvardie($result->getMessages());
                    throw new Exception($result->getMessages()[0]);
                }
            } else {
                $isLoginError = true;
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }

        return new JsonModel([
            'isLoginError' => $isLoginError,
            'redirectUrl' => $redirectUrl,
            'error' => $error
        ]);
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
                    throw new Exception("Email inválido");
                }
                $this->userManager->createUser($data['fEmail'], $data['fPass']);
                $this->redirect()->toRoute('home');
            } else {
                $this->redirect()->toRoute('home');
            }
        } catch (Exception $e) {
            return new JsonModel(
                ["code" => $e->getCode(),
                    "message" => $e->getMessage()]
            );
        }
        return $this->getResponse();
    }
}