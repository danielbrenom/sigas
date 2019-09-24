<?php


namespace Application\Entity\Sis;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="sis_professional_healthcare_accept")
 */
class ProfessionalHealthcares
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id_professional")
     */
    protected $id_professional;
    /**
     * @ORM\Id
     * @ORM\Column(name="id_healthcare")
     */
    protected $id_healthcare;

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
    public function getIdHealthcare()
    {
        return $this->id_healthcare;
    }

    /**
     * @param mixed $id_healthcare
     */
    public function setIdHealthcare($id_healthcare)
    {
        $this->id_healthcare = $id_healthcare;
    }

}