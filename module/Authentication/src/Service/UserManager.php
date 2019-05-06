<?php


namespace Authentication\Service;

use Authentication\Entity\Seg\User;
use Doctrine\ORM\EntityManager;
use Zend\Crypt\Password\Bcrypt;

class UserManager
{
    /**
     * @var $entityManager EntityManager
     */
    private $entityManager;

    /**
     * UserManager constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    /**
     * This method checks if at least one user presents, and if not, creates
     * 'Admin' user with email 'admin@example.com' and password 'Secur1ty'.
     */
    public function createAdminUserIfNotExists()
    {
        $user = $this->entityManager->getRepository(User::class)->findOneByEmail('admin@sigas.com');
        if ($user === null) {
            $user = new User();
            $user->setEmail('admin@sigas.com');
            $bcrypt = new Bcrypt();
            $passwordHash = $bcrypt->create('SigasAdmin');
            $user->setUserPassword($passwordHash);
            $user->setCreationDate(date('Y-m-d H:i:s'));

            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
    }

    public function createUser($user, $password)
    {
        $user = $this->entityManager->getRepository(User::class)->findOneByEmail($user);
        /**  @TODO: Criar usuário e também suas informações
         *   Inserir as informações na tabela de seg usuario, sis usuario e info usuario
         * */

        if ($user === null) {
            $newUser = new User();
            $newUser->setEmail($user);
            $bcrypt = new Bcrypt();
            $newUser->setUserPassword($bcrypt->create($password));
            $newUser->setCreationDate(date('Y-m-d H:i:s'));
            $this->entityManager->persist($newUser);
            $this->entityManager->flush();
            return true;
        }

        throw new \Exception("Este email já foi utilizado", -1);
    }
}