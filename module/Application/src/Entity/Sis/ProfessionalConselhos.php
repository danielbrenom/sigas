<?php


namespace Application\Entity\Sis;

use Doctrine\ORM\Mapping as ORM;

/**
 * Doctrine entity
 * @ORM\Entity
 * @ORM\Table(name="sis_professional_conselhos")
 */
class ProfessionalConselhos
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id_conselho")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(name="desc_conselho")
     */
    protected $desc_conselho;

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
    public function getDescConselho()
    {
        return $this->desc_conselho;
    }

    /**
     * @param mixed $desc_conselho
     */
    public function setDescConselho($desc_conselho)
    {
        $this->desc_conselho = $desc_conselho;
    }


}