<?php


namespace Application\Entity\Seg;

use Doctrine\ORM\Mapping as ORM;

/**
 * Doctrine entity
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
    protected $id;

    /**
     * @ORM\Column(name="id_user_type")
     */
    protected $id_user_type;

    /**
     * @ORM\Column(name="user_login")
     */
    protected $user_login;

    /**
     * @ORM\Column(name="creation_date")
     */
    protected $creation_date;

    /**
     * @ORM\OneToOne(targetEntity="Application\Entity\Sis\UserInfoPessoal")
     * @ORM\JoinColumn(name="id_user", referencedColumnName="id_user")
     */
    protected $user_information;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getIdUserType()
    {
        return $this->id_user_type;
    }

    /**
     * @param mixed $id_user_type
     */
    public function setIdUserType($id_user_type)
    {
        $this->id_user_type = $id_user_type;
    }

    /**
     * @return mixed
     */
    public function getUserLogin()
    {
        return $this->user_login;
    }

    /**
     * @param mixed $user_login
     */
    public function setUserLogin($user_login)
    {
        $this->user_login = $user_login;
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


}