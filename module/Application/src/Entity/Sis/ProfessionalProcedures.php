<?php


namespace Application\Entity\Sis;

use Doctrine\ORM\Mapping as ORM;

/**
 * Doctrine entity
 * @ORM\Entity
 * @ORM\Table(name="sis_professional_procedures")
 */
class ProfessionalProcedures
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id_procedure")
     */
    protected $id_procedure;

    /**
     * @ORM\Id
     * @ORM\Column(name="id_professional")
     */
    protected $id_professional;

    /**
     * @return mixed
     */
    public function getIdProcedure()
    {
        return $this->id_procedure;
    }

    /**
     * @param mixed $id_procedure
     */
    public function setIdProcedure($id_procedure)
    {
        $this->id_procedure = $id_procedure;
    }

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


}