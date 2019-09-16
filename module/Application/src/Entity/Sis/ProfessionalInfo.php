<?php


namespace Application\Entity\Sis;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="sis_professional_info")
 */
class ProfessionalInfo
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id_user")
     */
    protected $id_user;

    /**
     * @ORM\Column(name="id_especiality")
     */
    protected $id_especiality;

    /**
     * @ORM\Column(name="especiality_solicited")
     */
    protected $especiality_solicited;

    /**
     * @ORM\Column(name="prof_addresses")
     */
    protected $prof_addresses;

    /**
     * @ORM\Column(name="cons_name")
     */
    protected $cons_name;

    /**
     * @ORM\Column(name="cons_registry")
     */
    protected $cons_registry;

    /**
     * @ORM\Column(name="solicited_in")
     */
    protected $solicited_in;

    /**
     * @ORM\Column(name="confirmed_in")
     */
    protected $confirmed_in;

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
    public function getEspecialitySolicited()
    {
        return $this->especiality_solicited;
    }

    /**
     * @param mixed $especiality_solicited
     */
    public function setEspecialitySolicited($especiality_solicited)
    {
        $this->especiality_solicited = $especiality_solicited;
    }

    /**
     * @return mixed
     */
    public function getConsName()
    {
        return $this->cons_name;
    }

    /**
     * @param mixed $cons_name
     */
    public function setConsName($cons_name)
    {
        $this->cons_name = $cons_name;
    }

    /**
     * @return mixed
     */
    public function getConsRegistry()
    {
        return $this->cons_registry;
    }

    /**
     * @param mixed $cons_registry
     */
    public function setConsRegistry($cons_registry)
    {
        $this->cons_registry = $cons_registry;
    }

    /**
     * @return mixed
     */
    public function getSolicitedIn()
    {
        return $this->solicited_in;
    }

    /**
     * @param mixed $solicited_in
     */
    public function setSolicitedIn($solicited_in)
    {
        $this->solicited_in = $solicited_in;
    }

    /**
     * @return mixed
     */
    public function getConfirmedIn()
    {
        return $this->confirmed_in;
    }

    /**
     * @param mixed $confirmed_in
     */
    public function setConfirmedIn($confirmed_in)
    {
        $this->confirmed_in = $confirmed_in;
    }

    /**
     * @return mixed
     */
    public function getProfAddresses()
    {
        return $this->prof_addresses;
    }

    /**
     * @param mixed $prof_addresses
     */
    public function setProfAddresses($prof_addresses)
    {
        $this->prof_addresses = $prof_addresses;
    }
}