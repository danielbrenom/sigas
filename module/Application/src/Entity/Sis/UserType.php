<?php
/**
 * Created by PhpStorm.
 * User: 400005
 * Date: 15/03/2019
 * Time: 10:04
 */

namespace Application\Entity\Sis;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="sis_user_type")
 */
class UserType
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id_type")
     */
    protected $id;

    /**
     * @ORM\Column(name="type_Desc")
     */
    protected $type_Desc;
}