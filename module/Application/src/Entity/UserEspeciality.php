<?php
/**
 * Created by PhpStorm.
 * User: 400005
 * Date: 14/03/2019
 * Time: 12:13
 */

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="sis_user_espc")
 */
class UserEspeciality
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id_especialidade")
     */
    protected $id;
    /**
     * @ORM\Column(name="desc_especialidade")
     */
    protected $desc_especialidade;

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
    public function getDescEspecialidade()
    {
        return $this->desc_especialidade;
    }

    /**
     * @param mixed $desc_especialidade
     */
    public function setDescEspecialidade($desc_especialidade)
    {
        $this->desc_especialidade = $desc_especialidade;
    }


}