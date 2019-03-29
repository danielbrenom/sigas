<?php
/**
 * Created by PhpStorm.
 * User: 400005
 * Date: 29/03/2019
 * Time: 11:57
 */

namespace Application\Entity\Sis;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="sis_user_appointments")
 */
class UserAppointment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id_appointment")
     */
    protected $id;
    /**
     * @ORM\Column(name="id_user_ps")
     */
    protected $id_user_ps;
    /**
     * @ORM\Column(name="id_especiality")
     */
    protected $id_especiality;
    /**
     * @ORM\Column(name="solicited_for")
     */
    protected $solicited_for;
    /**
     * @ORM\Column(name="created_on")
     */
    protected $created_on;
}