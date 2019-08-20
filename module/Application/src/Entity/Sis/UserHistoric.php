<?php


namespace Application\Entity\Sis;


class UserHistoric
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id_historic_entry")
     */
    protected $id_historic;

    /**
     * @ORM\OneToOne(targetEntity="User",mappedBy="user_historic")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id_user")
     */
    protected $user_id;

    /**
     * @ORM\Column(name="id_type_registry")
     */
    protected $id_type_registry;

    /**
     * @ORM\OneToOne(targetEntity="UserAppointment", inversedBy="id")
     * @ORM\JoinColumn(name="id_appointment_entry", referencedColumnName="id")
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
    public function getIdTypeRegistry()
    {
        return $this->id_type_registry;
    }

    /**
     * @param mixed $id_type_registry
     */
    public function setIdTypeRegistry($id_type_registry)
    {
        $this->id_type_registry = $id_type_registry;
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



}