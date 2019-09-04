<?php


namespace Application\Entity\Sis;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="sis_user_historic_type")
 */
class UserHistoricType
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id_type")
     */
    protected $id;

    /**
     * @ORM\Column(name="historic_type_description")
     */
    protected $historic_type_description;

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
    public function getHistoricTypeDescription()
    {
        return $this->historic_type_description;
    }

    /**
     * @param mixed $historic_type_description
     */
    public function setHistoricTypeDescription($historic_type_description)
    {
        $this->historic_type_description = $historic_type_description;
    }


}