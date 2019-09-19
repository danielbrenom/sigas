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
use Exception;
use Zend\Crypt\Password\Bcrypt;
use Zend\Json\Json;
use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Math\Rand;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;

class UserManager
{
    /**
     * @var $entityManager EntityManager
     */
    private $entityManager;

    private $viewRenderer;

    /**
     * UserManager constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager, $viewRenderer)
    {
        $this->entityManager = $entityManager;
        $this->viewRenderer = $viewRenderer;
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
     * @throws Exception
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
                $profInfo->setProfAddresses(Json::encode($data['fAddress']));
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

    /**
     * Criação de um usuário de atendente
     * @param array $data Informações do atendent
     * @return bool
     * @throws Exception
     */
    public function createAttendant($data){
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
                $newUser->setIdUserType(3);
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
                $userPersonalInfo->setUserAddr($data['fAddress']);
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
        return false;
    }

    public function generatePasswordResetToken($params)
    {
        try {
            $this->entityManager->beginTransaction();
            /**@var $user User */
            $user = $this->entityManager->getRepository(User::class)->findOneBy(["email" => $params['fEmail']]);
            if ($user === null) {
                throw new \Exception("Usuário não encontrado");
            }
//            if ($user->getAtivo() != User::STATUS_ACTIVE) {
//                throw new \Exception('Não é possível gera um token para usuário inativo.');
//            }
            $token = Rand::getString(32, '0123456789abcdefghijklmnopqrstuvwxyz');
            $bcrypt = new Bcrypt();
            $tokenHash = $bcrypt->create($token);
            $user->setPwdResetToken($tokenHash);
            $tokenDate = new \DateTime('now', new \DateTimeZone('America/Belem'));
            $user->setPwdResetCreationDate($tokenDate->format('Y-m-d H:i:s'));
            $this->entityManager->flush();
            $assunto = "Redefinição de senha";
            $httpHost = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
            $passwordResetUrl = "http://{$httpHost}/reseta?token={$token}&email={$user->getEmail()}";
            $bodyHtml = $this->viewRenderer->render('authentication/email/reset-password',
                ['passwordResetUrl' => $passwordResetUrl,]);
            $html = new MimePart($bodyHtml);
            $html->type = "text/html";
            $body = new MimeMessage();
            $body->addPart($html);
            $mail = new Message();
            $mail->setEncoding('UTF-8');
            $mail->setBody($body);
            $mail->setFrom('no-reply@sigas.com', 'Administrador');
            $mail->setSubject($assunto);
            $mail->addTo($user->getEmail(), "Usuário do SIGAS");
            $transport = new SmtpTransport();
            $config = new SmtpOptions([
                'name' => 'gmail',
                'host' => 'smtp.gmail.com',
                'port' => 587,
                'connection_class' => 'plain',
                'connection_config' => [
                    'username' => 'mpcontaspa@gmail.com',
                    'password' => 'Empc2016',
                    'ssl' => 'tls'
                ]
            ]);
            $transport->setOptions($config);
            $transport->send($mail);
            $this->entityManager->commit();
            return true;
            UtilsFile::printvardie("Email enviado",$user, $params);
        } catch (Exception $e) {
            $this->entityManager->rollback();
            return false;
            UtilsFile::printvardie($e->getMessage());
        }
    }

    public function validatePasswordResetToken($email, $token){

        try {
            /**@var $user User */
            $user = $this->entityManager->getRepository(User::class)->findOneBy(["email" => $email]);
//            if ($user === null || $user->getAtivo() != User::STATUS_ACTIVE) {
//                return false;
//            }
            $bcrypt = new Bcrypt();
            $tokenHash = $user->getPwdResetToken();
            if (!$bcrypt->verify($token, $tokenHash)) {
                return false;
            }
            $tokenCreationDate = $user->getPwdResetCreationDate();
            $tokenCreationDate = new \DateTime($tokenCreationDate, new \DateTimeZone("America/Belem"));
            $currentDate = new \DateTime('now', new \DateTimeZone("America/Belem"));
            $diff = $currentDate->diff($tokenCreationDate);
//            UtilsFile::printvardie($tokenCreationDate,$currentDate,$diff);
            if ($diff->d > 1) {
                return false; // expired
            }
            return true;
        } catch (Exception $e) {
            return $e->getMessage();
            return false;
        }
    }

    public function resetPasswordFromToken($email,$token, $newPassword){
        try {
            if (!$this->validatePasswordResetToken($email, $token)) {
                return false;
            }
            /**@var $user User */
            $user = $this->entityManager->getRepository(User::class)->findOneBy(["email" => $email]);
//            if ($user == null || $user->getAtivo() != Usuario::STATUS_ACTIVE) {
//                return false;
//            }
            $bcrypt = new Bcrypt();
            $passwordHash = $bcrypt->create($newPassword);
            $user->setUserPassword($passwordHash);
            $user->setPwdResetToken(null);
            $user->setPwdResetCreationDate(null);
            $this->entityManager->flush();
            return true;
        } catch (Exception $e) {
            return $e->getMessage();
            throw $e;
        }
    }
}