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
    public function getIdUserPs()
    {
        return $this->id_user_ps;
    }

    /**
     * @param mixed $id_user_ps
     */
    public function setIdUserPs($id_user_ps)
    {
        $this->id_user_ps = $id_user_ps;
    }

    /**
     * @return mixed
     */
    public function getIdEspeciality()
    {
        return $this->id_especiality;
    }

    /**
     * @param mixed $id_especiality
     */
    public function setIdEspeciality($id_especiality)
    {
        $this->id_especiality = $id_especiality;
    }

    /**
     * @return mixed
     */
    public function getSolicitedFor()
    {
        return $this->solicited_for;
    }

    /**
     * @param mixed $solicited_for
     */
    public function setSolicitedFor($solicited_for)
    {
        $this->solicited_for = $solicited_for;
    }

    /**
     * @return mixed
     */
    public function getCreatedOn()
    {
        return $this->created_on;
    }

    /**
     * @param mixed $created_on
     */
    public function setCreatedOn($created_on)
    {
        $this->created_on = $created_on;
    }
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