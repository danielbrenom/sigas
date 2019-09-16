<?php


namespace Application\Entity\Sis;

use Doctrine\ORM\Mapping as ORM;

/**
 * Doctrine entity
 * @ORM\Entity
 * @ORM\Table(name="sis_professional_attendee")
 */
class ProfessionalAttendee
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id_professional")
     */
    protected $id_professional;
    /**
     * @ORM\Id
     * @ORM\Column(name="id_attendant")
     */
    protected $id_attendant;
    /**
     * @ORM\Id
     * @ORM\Column(name="dt_inicio")
     */
    protected $dt_inicio;

    /**
     * @ORM\Column(name="dt_fim")
     */
    protected $dt_fim;

    /**
     * @return mixed
     */
    public function getIdProfessional()
    {
        return $this->id_professional;
    }

    /**
     * @param mixed $id_professional
     */
    public function setIdProfessional($id_professional)
    {
        $this->id_professional = $id_professional;
    }

    /**
     * @return mixed
     */
    public function getIdAttendee()
    {
        return $this->id_attendant;
    }

    /**
     * @param mixed $id_attendant
     */
    public function setIdAttendant($id_attendant)
    {
        $this->id_attendant = $id_attendant;
    }

    /**
     * @return mixed
     */
    public function getDtInicio()
    {
        return $this->dt_inicio;
    }

    /**
     * @param mixed $dt_inicio
     */
    public function setDtInicio($dt_inicio)
    {
        $this->dt_inicio = $dt_inicio;
    }

    /**
     * @return mixed
     */
    public function getDtFim()
    {
        return $this->dt_fim;
    }

    /**
     * @param mixed $dt_fim
     */
    public function setDtFim($dt_fim)
    {
        $this->dt_fim = $dt_fim;
    }


}