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
     * @ORM\GeneratedValue
     * @ORM\Column(name="id_user")
     */
    protected $id;

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
}