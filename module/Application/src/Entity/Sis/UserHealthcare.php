<?php


namespace Application\Entity\Sis;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="sis_user_healthcare")
 */
class UserHealthcare
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id_healthcare")
     */
    protected $id;

    /**
     * @ORM\Column(name="desc_healthcare")
     */
    protected $desc_healthcare;

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
    public function getDescHealthcare()
    {
        return $this->desc_healthcare;
    }

    /**
     * @param mixed $desc_healthcare
     */
    public function setDescHealthcare($desc_healthcare)
    {
        $this->desc_healthcare = $desc_healthcare;
    }


}