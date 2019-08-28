<?php


namespace Application\Entity\Sis;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="sis_user_historic")
 */
class UserHistoric
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id_historic_entry")
     */
    protected $id_historic;

    /**
     * @ORM\OneToOne(targetEntity="Application\Entity\Seg\User",mappedBy="user_historic")
     * @ORM\JoinColumn(name="id_user", referencedColumnName="id_user")
     */
    protected $user_id;

    /**
     * @ORM\Column(name="historic_type")
     */
    protected $historic_type;

    /**
     * @ORM\Column(name="id_reg_informations")
     */
    protected $id_reg_informations;

    /**
     * @ORM\OneToOne(targetEntity="UserAppointment", inversedBy="id")
     * @ORM\JoinColumn(name="id_appointment_entry", referencedColumnName="id_appointment")
     */
    protected $id_appointment_entry;

    /**
     * @return mixed
     */
    public function getIdHistoric()
    {
        return $this->id_historic;
    }

    /**
     * @param mixed $id_historic
     */
    public function setIdHistoric($id_historic)
    {
        $this->id_historic = $id_historic;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param mixed $user_id
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * @return mixed
     */
    public function getIdReginformation()
    {
        return $this->id_reg_informations;
    }

    /**
     * @param mixed $id_reg_informations
     */
    public function setIdReginformation($id_reg_informations)
    {
        $this->id_reg_informations = $id_reg_informations;
    }

    /**
     * @return mixed
     */
    public function getIdAppointmentEntry()
    {
        return $this->id_appointment_entry;
    }

    /**
     * @param mixed $id_appointment_entry
     */
    public function setIdAppointmentEntry($id_appointment_entry)
    {
        $this->id_appointment_entry = $id_appointment_entry;
    }

    /**
     * @return mixed
     */
    public function getHistoricType()
    {
        return $this->historic_type;
    }

    /**
     * @param mixed $historic_type
     */
    public function setHistoricType($historic_type)
    {
        $this->historic_type = $historic_type;
    }

    /**
     * @return mixed
     */
    public function getIdRegInformations()
    {
        return $this->id_reg_informations;
    }

    /**
     * @param mixed $id_reg_informations
     */
    public function setIdRegInformations($id_reg_informations)
    {
        $this->id_reg_informations = $id_reg_informations;
    }



}