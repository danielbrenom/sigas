<?php


namespace Application\Entity\Sis;

use Doctrine\ORM\Mapping as ORM;

/**
 * Doctrine entity
 * @ORM\Entity
 * @ORM\Table(name="sis_procedures")
 */
class Procedures
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id_procedure")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(name="procedure_description")
     */
    protected $procedure_description;

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
    public function getProcedureDescription()
    {
        return $this->procedure_description;
    }

    /**
     * @param mixed $procedure_description
     */
    public function setProcedureDescription($procedure_description)
    {
        $this->procedure_description = $procedure_description;
    }


}