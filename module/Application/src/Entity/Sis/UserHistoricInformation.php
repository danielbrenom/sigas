<?php


namespace Application\Entity\Sis;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="sis_user_historic_information")
 */
class UserHistoricInformation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id_reg")
     */
    protected $id;

    /**
     * @ORM\Column(name="id_historic_reg")
     */
    protected $id_historic_reg;

    /**
     * @ORM\Column(name="historic_information")
     */
    protected $historic_information;

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
        return $this->historic_information;
    }

    /**
     * @param mixed $historic_information
     */
    public function setHistoricTypeDescription($historic_information)
    {
        $this->historic_information = $historic_information;
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