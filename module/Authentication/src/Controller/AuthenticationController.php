<?php


namespace Authentication\Controller;


use Application\Repository\MobileRepository;
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
use Zend\Validator\Regex;
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
        try {
            $this->authManager->logout();
            return $this->redirect()->toRoute('application_mobile_front');
        } catch (Exception $e) {
            return $this->redirect()->toRoute('application_mobile_front');
        }
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

    public function singupProfAction()
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
                if ($this->userManager->createProfessional($data))
                    $this->mobileRepository->setMessage("Solicitação enviada com sucesso. \n Seu usuário foi criado, assim que for confirmado seu perfil de profissional será ativado.", 1);
                else
                    $this->mobileRepository->setMessage("Não foi possível processar sua solicitação. \n Tente novamente mais tarde", 0);
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

    public function singupAttendantAction()
    {
        try {
            if ($this->getRequest()->isPost()) {
                $emailValidator = new EmailAddress([
                    "allow" => Hostname::ALLOW_DNS | Hostname::ALLOW_IP | Hostname::ALLOW_LOCAL,
                    "mxCheck" => true,
                    'deepMxCheck' => true
                ]);
                $data = $this->params()->fromPost();
                if (!$emailValidator->isValid($data['fEmail'])) {
                    throw new Exception('Email inválido');
                }
                if ($this->userManager->createAttendant($data))
                    $this->mobileRepository->setMessage("Usuário criado com sucesso, seu perfil será confirmado assim que possível.", 1);
                else
                    $this->mobileRepository->setMessage("Não foi possível processar sua solicitação. \n Tente novamente mais tarde", 0);
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

    public function recuperaAction()
    {
        try {
            if ($this->getRequest()->isPost()) {
                $params = $this->params()->fromPost();
                if ($this->userManager->generatePasswordResetToken($params)) {
                    $this->mobileRepository->setMessage("A solicitação de redefinição de senha foi enviada para seu email", 1);
                } else {
                    $this->mobileRepository->setMessage("Ocorreu um erro, tente novamente mais tarde", 0);
                }
                return $this->redirect()->toRoute('application_mobile_front');
            }
        } catch (Exception $e) {
            $this->mobileRepository->setMessage("Ocorreu um erro, tente novamente mais tarde", 0);
            return $this->redirect()->toRoute('application_mobile_front');
            UtilsFile::printvardie($e->getMessage());
        }
    }

    public function resetaAction()
    {
        try {
            if ($this->getRequest()->isPost()) {
                $params = $this->params()->fromPost();
                $matricValidator = new Regex(['pattern' => "/^{$params['fSenha']}$/"]);
                if ($matricValidator->isValid($params['fSenhaConf'])) {
                    //UtilsFile::printvardie($this->userManager->resetPasswordFromToken($params['fEm'], $params['fToken'], $params['fSenha']));;
                    if ($this->userManager->resetPasswordFromToken($params['fEm'], $params['fToken'], $params['fSenha'])) {
                        $this->mobileRepository->setMessage("Senha redefinida com sucesso", 1);
                        return $this->redirect()->toRoute('application_mobile_front');
                    }
                    $this->mobileRepository->setMessage("Erro ao redefinir senha, tente novamente", 0);
                    return $this->redirect()->toUrl("/reseta?token={$params['fToken']}&email={$params['fEm']}");
                    //Mostrar Erro
                    //throw new Exception("Erro ao alterar senha");
                }
                $this->mobileRepository->setMessage("As senhas não coicidem, tente novamente", 0);
                return $this->redirect()->toUrl("/reseta?token={$params['token']}&email?={$params['email']}");
            }
            if ($this->getRequest()->isGet()) {
                $params = $this->params()->fromQuery();
                if ($params['token'] != null && (!is_string($params['token']) || strlen($params['token']) != 32)) {
                    $this->mobileRepository->setMessage("Token inválido, solicite redefinição de senha novamente", 0);
                    return $this->redirect()->toRoute('application_mobile_front');
                }
                if ($params['token'] === null || !$this->userManager->validatePasswordResetToken($params['email'], $params['token'])) {
                    $this->mobileRepository->setMessage("Token inválido, solicite redefinição de senha novamente", 0);
                    return $this->redirect()->toRoute('application_mobile_front');
                }
                return new ViewModel([
                    'email' => $params['email'],
                    'token' => $params['token']
                ]);
            }
            return $this->getResponse()->setStatusCode(400);
        } catch (Exception $e) {
            UtilsFile::printvardie($e->getMessage());
            return $this->getResponse();
        }
    }
}