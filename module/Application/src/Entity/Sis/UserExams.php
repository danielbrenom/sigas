<?php


namespace Application\Entity\Sis;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="sis_user_exams")
 */
class UserExams
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id_exam")
     */
    protected $id;

    /**
     * @ORM\Column(name="exam_name")
     */
    protected $exam_name;

    /**
     * @ORM\Column(name="exam_codigo")
     */
    protected $exam_codigo;
    /**
     * @ORM\Column(name="exam_notes")
     */
    protected $exam_notes;

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
    public function getExamName()
    {
        return $this->exam_name;
    }

    /**
     * @param mixed $exam_name
     */
    public function setExamName($exam_name)
    {
        $this->exam_name = $exam_name;
    }

    /**
     * @return mixed
     */
    public function getExamCodigo()
    {
        return $this->exam_codigo;
    }

    /**
     * @param mixed $exam_codigo
     */
    public function setExamCodigo($exam_codigo)
    {
        $this->exam_codigo = $exam_codigo;
    }

    /**
     * @return mixed
     */
    public function getExamNotes()
    {
        return $this->exam_notes;
    }

    /**
     * @param mixed $exam_notes
     */
    public function setExamNotes($exam_notes)
    {
        $this->exam_notes = $exam_notes;
    }


}