<?php


namespace Authentication\Entity\Seg;

use Doctrine\ORM\Mapping as ORM;

/**
 * Doctrine Entity
 * @ORM\Entity()
 * @ORM\Table(name="seg_user")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id_user")
     * @ORM\GeneratedValue
     */
    protected $id_user;

    /**
     * @ORM\Column(name="user_login")
     */
    protected $email;

    /**
     * @ORM\Column(name="user_password")
     */
    protected $user_password;

    /**
     * @ORM\Column(name="creation_date")
     */
    protected $creation_date;

    /**
     * @ORM\Column(name="pwd_reset_token")
     */
    protected $pwd_reset_token;

    /**
     * @ORM\Column(name="pwd_reset_creation_date")
     */
    protected $pwd_reset_creation_date;

    /**
     * @return mixed
     */
    public function getIdUser()
    {
        return $this->id_user;
    }

    /**
     * @param mixed $id_user
     */
    public function setIdUser($id_user)
    {
        $this->id_user = $id_user;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getUserPassword()
    {
        return $this->user_password;
    }

    /**
     * @param mixed $user_password
     */
    public function setUserPassword($user_password)
    {
        $this->user_password = $user_password;
    }

    /**
     * @return mixed
     */
    public function getCreationDate()
    {
        return $this->creation_date;
    }

    /**
     * @param mixed $creation_date
     */
    public function setCreationDate($creation_date)
    {
        $this->creation_date = $creation_date;
    }

    /**
     * @return mixed
     */
    public function getPwdResetToken()
    {
        return $this->pwd_reset_token;
    }

    /**
     * @param mixed $pwd_reset_token
     */
    public function setPwdResetToken($pwd_reset_token)
    {
        $this->pwd_reset_token = $pwd_reset_token;
    }

    /**
     * @return mixed
     */
    public function getPwdResetCreationDate()
    {
        return $this->pwd_reset_creation_date;
    }

    /**
     * @param mixed $pwd_reset_creation_date
     */
    public function setPwdResetCreationDate($pwd_reset_creation_date)
    {
        $this->pwd_reset_creation_date = $pwd_reset_creation_date;
    }
}