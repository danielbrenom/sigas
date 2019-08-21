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
     * @ORM\Column(name="sis_user_historic_typecol")
     */
    protected $sis_user_historic_typecol;

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

    /**
     * @return mixed
     */
    public function getSisUserHistoricTypecol()
    {
        return $this->sis_user_historic_typecol;
    }

    /**
     * @param mixed $sis_user_historic_typecol
     */
    public function setSisUserHistoricTypecol($sis_user_historic_typecol)
    {
        $this->sis_user_historic_typecol = $sis_user_historic_typecol;
    }


}