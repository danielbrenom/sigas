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
}