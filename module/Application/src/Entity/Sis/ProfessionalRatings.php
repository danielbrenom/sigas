<?php


namespace Application\Entity\Sis;

use Doctrine\ORM\Mapping as ORM;

/**
 * Doctrine entity
 * @ORM\Entity
 * @ORM\Table(name="sis_professional_ratings")
 */
class ProfessionalRatings
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id")
     * @ORM\GeneratedValue
     */
    protected $id;
    /**
     * @ORM\Column(name="id_professional")
     */
    protected $id_professional;
    /**
     * @ORM\Column(name="id_user")
     */
    protected $id_user;
    /**
     * @ORM\Column(name="rating_comment")
     */
    protected $rating_comment;
    /**
     * @ORM\Column(name="rating_stars")
     */
    protected $rating_stars;

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
    public function getIdUser()
    {
        return $this->id_user;
    }

    /**
     * @param mixed $id_user
     */
    public function setIdUser($id_user)
    {
        $this->id_user = $id_user;
    }

    /**
     * @return mixed
     */
    public function getRatingComment()
    {
        return $this->rating_comment;
    }

    /**
     * @param mixed $rating_comment
     */
    public function setRatingComment($rating_comment)
    {
        $this->rating_comment = $rating_comment;
    }

    /**
     * @return mixed
     */
    public function getRatingStars()
    {
        return $this->rating_stars;
    }

    /**
     * @param mixed $rating_stars
     */
    public function setRatingStars($rating_stars)
    {
        $this->rating_stars = $rating_stars;
    }

}