<?php


namespace Authentication\Service;


use Exception;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Result;
use Zend\Session\SessionManager;

class AuthenticationManager
{
    /**
     * @var $authService AuthenticationService
     */
    private $authService;

    /**
     * @var $sessionManager SessionManager
     */
    private $sessionManager;

    /**
     * AuthenticationManager constructor.
     * @param AuthenticationService $authService
     * @param SessionManager $sessionManager
     */
    public function __construct(AuthenticationService $authService, SessionManager $sessionManager)
    {
        $this->authService = $authService;
        $this->sessionManager = $sessionManager;
    }

    /**
     * @param $email
     * @param $password
     * @param null $rememberMe
     * @return Result
     * @throws Exception
     */
    public function login($email, $password, $rememberMe = null)
    {
        if ($this->authService->getIdentity() !== null) {
            throw new Exception("Já logado");
        }
        /**
         * @var $authAdapter AuthenticationAdapter
         */
        $authAdapter = $this->authService->getAdapter();
        $authAdapter->setEmail($email);
        $authAdapter->setPassword($password);
        $result = $this->authService->authenticate();
        if ($result->isValid()) {
            $user = $this->authService->getAdapter()->getUserIdentity();
            $this->authService->getStorage()->write($user);
        }
        return $result;
    }

    public function logout()
    {
        if ($this->authService->getIdentity() === null) {
            throw new Exception("Não está logado");
        }
        $this->authService->clearIdentity();
    }

    public function userState()
    {
        return $this->authService->hasIdentity();
    }

    public function getActiveUser()
    {
        return $this->authService->getStorage()->read();
    }
}