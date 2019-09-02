<?php


namespace Authentication\Service;

use Application\Debug\UtilsFile;
use Application\Entity\Sis\ProfessionalInfo;
use Application\Entity\Sis\UserEspeciality;
use Application\Entity\Sis\UserInfoPessoal;
use Authentication\Entity\Seg\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
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

    /**
     * @param $user
     * @param $password
     * @return bool
     * @throws \Exception
     */
    public function createUser($email, $password)
    {
        $user = $this->entityManager->getRepository(User::class)->findOneByEmail($email);
        /**  @TODO: Criar usuário e também suas informações
         *   Inserir as informações na tabela de seg usuario e info usuario
         * */
        //UtilsFile::printvardie($user);
        try {
            if ($user === null) {
                $this->entityManager->beginTransaction();
                $newUser = new User();
                $newUser->setEmail($email);
                $bcrypt = new Bcrypt();
                $newUser->setUserPassword($bcrypt->create($password));
                $newUser->setCreationDate(date('Y-m-d H:i:s'));
                $this->entityManager->persist($newUser);
                $this->entityManager->flush();
                $lastId = $newUser->getIdUser();
                /** @var UserEspeciality $userEspec */
                $userEspec = $this->entityManager->getRepository(UserEspeciality::class)->find(1);
                $userPersonalInfo = new UserInfoPessoal();
                $userPersonalInfo->setId($lastId);
                $userPersonalInfo->setUserEmail($email);
                //$userPersonalInfo->setIdEspecialidade(1);
                $userPersonalInfo->setUserEspeciality($userEspec);
                $userPersonalInfo->setUserCpf(" ");
                $userPersonalInfo->setUserRg(" ");
                $userPersonalInfo->setUserAddr(" ");
                $this->entityManager->persist($userPersonalInfo);
                $this->entityManager->flush();
                $this->entityManager->commit();
                return true;
            }
        } catch (OptimisticLockException $e) {
            $this->entityManager->rollback();
            throw new \Exception($e->getMessage());
        } catch (ORMException $e) {
            $this->entityManager->rollback();
            throw new \Exception($e->getMessage());
        }

        throw new \Exception("Este email já foi utilizado", -1);
    }

    /**
     * Criação de um usuário de profissional
     * Cria um usuário comum e salva a solicitação de perfil de profissional
     * @param array $data
     * @return bool
     */
    public function createProfessional($data)
    {
        $user = $this->entityManager->getRepository(User::class)->findOneByEmail($data['fEmail']);
        try {
            if ($user === null) {
                $this->entityManager->beginTransaction();
                $timeSolicited = date('Y-m-d H:i:s');
                //Informações do usuário
                $newUser = new User();
                $newUser->setEmail($data['fEmail']);
                $bcrypt = new Bcrypt();
                $newUser->setUserPassword($bcrypt->create($data['fPass']));
                $newUser->setCreationDate($timeSolicited);
                $newUser->setIdUserType(2);
                $this->entityManager->persist($newUser);
                $this->entityManager->flush();
                $lastId = $newUser->getIdUser();
                /**
                 * Informações pessoais
                 * @var UserEspeciality $userEspec
                 */
                $userEspec = $this->entityManager->getRepository(UserEspeciality::class)->find(1);
                $userPersonalInfo = new UserInfoPessoal();
                $userPersonalInfo->setId($lastId);
                $userPersonalInfo->setUserEmail($data['fEmail']);
                $userPersonalInfo->setUserName($data['fName']);
                $userPersonalInfo->setUserEspeciality($userEspec);
                $userPersonalInfo->setUserCpf("");
                $userPersonalInfo->setUserRg("");
                $userPersonalInfo->setUserAddr("");
                $this->entityManager->persist($userPersonalInfo);
                $this->entityManager->flush();
                //Inserir solicitação nas tabela de info de profissional
                $profInfo = new ProfessionalInfo();
                $profInfo->setIdUser($lastId);
                $profInfo->setEspecialitySolicited($data['fEspeciality']);
                $profInfo->setConsName($data['fCons']);
                $profInfo->setConsRegistry($data['fNumCons']);
                $profInfo->setSolicitedIn($timeSolicited);
                $this->entityManager->persist($profInfo);
                $this->entityManager->flush();
                $this->entityManager->commit();
                return true;
            }
        } catch (OptimisticLockException $e) {
            $this->entityManager->rollback();
            throw new \Exception($e->getMessage());
        } catch (ORMException $e) {
            $this->entityManager->rollback();
            throw new \Exception($e->getMessage());
        }
        return false;
    }
}