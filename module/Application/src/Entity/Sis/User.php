<?php
/**
 * Created by PhpStorm.
 * User: 400005
 * Date: 15/03/2019
 * Time: 09:54
 */

namespace Application\Entity\Sis;

use Doctrine\ORM\Mapping as ORM;

/**
 * @deprecated Classe não mais utilizada
 * @ORM\Entity
 * @ORM\Table(name="sis_user")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id_user")
     */
    protected $id;

    /**
     * @ORM\Column(name="id_especialidade")
     */
    protected $id_especialidade;

    /**
     * @ORM\Column(name="id_user_type")
     */
    protected $id_user_type;

    /**
     * @ORM\OneToOne(targetEntity="UserType")
     * @ORM\JoinColumn(name="id_user_type", referencedColumnName="id_type")
     */
    protected $user_type;

    /**
     * @ORM\OneToOne(targetEntity="UserEspeciality")
     * @ORM\JoinColumn(name="id_especialidade", referencedColumnName="id_especialidade")
     */
    protected $user_especiality;

    /**
     * @ORM\OneToOne(targetEntity="UserInfoPessoal")
     * @ORM\JoinColumn(name="id_user", referencedColumnName="id_user")
     */
    protected $user_information;

    /**
     * @ORM\OneToOne(targetEntity="UserHistoric", inversedBy="user_id")
     * @ORM\JoinColumn(name="user_historic", referencedColumnName="id_user")
     */
    protected $user_historic;


}