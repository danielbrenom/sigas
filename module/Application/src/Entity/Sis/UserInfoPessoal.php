<?php
/**
 * Created by PhpStorm.
 * User: 400005
 * Date: 15/03/2019
 * Time: 10:11
 */

namespace Application\Entity\Sis;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="sis_usuario_info_pessoal")
 */
class UserInfoPessoal
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id_user")
     */
    protected $id;

    /**
     * @ORM\Column(name="id_especialidade")
     */
    protected $id_especialidade;

    /**
     * @ORM\Column(name="user_name")
     */
    protected $user_name;

    /**
     * @ORM\Column(name="user_cpf")
     */
    protected $user_cpf;
    /**
     * @ORM\Column(name="user_rg")
     */
    protected $user_rg;
    /**
     * @ORM\Column(name="user_cons_reg")
     */
    protected $user_cons_reg;
    /**
     * @ORM\Column(name="user_addr")
     */
    protected $user_addr;
    /**
     * @ORM\Column(name="user_email")
     */
    protected $user_email;
    /**
     * @ORM\Column(name="user_birthdate")
     */
    protected $user_birthdate;
    /**
     * @ORM\Column(name="user_healthcare")
     */
    protected $user_healthcare;
    /**
     * @ORM\Column(name="user_ctt_phone")
     */
    protected $user_ctt_phone;
    /**
     * @ORM\Column(name="user_ctt_res")
     */
    protected $user_ctt_res;

    /**
     * @ORM\OneToOne(targetEntity="UserEspeciality")
     * @ORM\JoinColumn(name="id_especialidade", referencedColumnName="id_especialidade")
     */
    protected $user_especiality;

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
    public function getUserName()
    {
        return $this->user_name;
    }

    /**
     * @param mixed $user_name
     */
    public function setUserName($user_name)
    {
        $this->user_name = $user_name;
    }

    /**
     * @return mixed
     */
    public function getUserCpf()
    {
        return $this->user_cpf;
    }

    /**
     * @param mixed $user_cpf
     */
    public function setUserCpf($user_cpf)
    {
        $this->user_cpf = $user_cpf;
    }

    /**
     * @return mixed
     */
    public function getUserRg()
    {
        return $this->user_rg;
    }

    /**
     * @param mixed $user_rg
     */
    public function setUserRg($user_rg)
    {
        $this->user_rg = $user_rg;
    }

    /**
     * @return mixed
     */
    public function getUserConsReg()
    {
        return $this->user_cons_reg;
    }

    /**
     * @param mixed $user_cons_reg
     */
    public function setUserConsReg($user_cons_reg)
    {
        $this->user_cons_reg = $user_cons_reg;
    }

    /**
     * @return mixed
     */
    public function getUserAddr()
    {
        return $this->user_addr;
    }

    /**
     * @param mixed $user_addr
     */
    public function setUserAddr($user_addr)
    {
        $this->user_addr = $user_addr;
    }

    /**
     * @return mixed
     */
    public function getUserEmail()
    {
        return $this->user_email;
    }

    /**
     * @param mixed $user_email
     */
    public function setUserEmail($user_email)
    {
        $this->user_email = $user_email;
    }

    /**
     * @return mixed
     */
    public function getUserBirthdate()
    {
        return $this->user_birthdate;
    }

    /**
     * @param mixed $user_birthdate
     */
    public function setUserBirthdate($user_birthdate)
    {
        $this->user_birthdate = $user_birthdate;
    }

    /**
     * @return mixed
     */
    public function getUserHealthcare()
    {
        return $this->user_healthcare;
    }

    /**
     * @param mixed $user_healthcare
     */
    public function setUserHealthcare($user_healthcare)
    {
        $this->user_healthcare = $user_healthcare;
    }

    /**
     * @return mixed
     */
    public function getUserCttPhone()
    {
        return $this->user_ctt_phone;
    }

    /**
     * @param mixed $user_ctt_phone
     */
    public function setUserCttPhone($user_ctt_phone)
    {
        $this->user_ctt_phone = $user_ctt_phone;
    }

    /**
     * @return mixed
     */
    public function getUserCttRes()
    {
        return $this->user_ctt_res;
    }

    /**
     * @param mixed $user_ctt_res
     */
    public function setUserCttRes($user_ctt_res)
    {
        $this->user_ctt_res = $user_ctt_res;
    }

    /**
     * @return mixed
     */
    public function getIdEspecialidade()
    {
        return $this->id_especialidade;
    }

    /**
     * @param mixed $id_especialidade
     */
    public function setIdEspecialidade($id_especialidade)
    {
        $this->id_especialidade = $id_especialidade;
    }



}