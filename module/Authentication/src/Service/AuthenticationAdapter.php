<?php


namespace Authentication\Service;


use Application\Debug\UtilsFile;
use Authentication\Entity\Seg\User;
use Zend\Authentication\Adapter\AdapterInterface;
use Doctrine\ORM\EntityManager;
use Zend\Authentication\Adapter\Exception\ExceptionInterface;
use Zend\Authentication\Result;
use Zend\Crypt\Password\Bcrypt;

class AuthenticationAdapter implements AdapterInterface
{

    private $email;
    private $password;
    /**
     * Entity Manager
     * @var $entityManager EntityManager;
     */
    private $entityManager;

    public function __construct($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Performs an authentication attempt
     *
     * @return Result
     * @throws ExceptionInterface If authentication cannot be performed
     */
    public function authenticate()
    {
        /**
         * @var $user User
         */
        $user = $this->entityManager->getRepository(User::class)->findOneByEmail($this->email);

        if ($user === null) {
            return new Result(Result::FAILURE_IDENTITY_NOT_FOUND, null, ['Email inválido']);
        }
        $bcrypt = new Bcrypt();
        $passwordHash = $user->getUserPassword();

        if ($bcrypt->verify($this->password, $passwordHash)) {
            return new Result(Result::SUCCESS, $this->email, ['Autenticado']);
        }
        return new Result(Result::FAILURE_CREDENTIAL_INVALID, null, ['Senha incorreta']);
    }

    public function getUserIdentity()
    {
        /**
         * @var $user User
         */
        $user = $this->entityManager->getRepository(User::class)->findOneByEmail($this->email);
        return [
            'user_id' => $user->getIdUser(),
            'user_login' => $user->getEmail()
        ];
    }
}