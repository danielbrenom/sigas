<?php


namespace Application\Entity\Sis;

use Doctrine\ORM\Mapping as ORM;

/**
 * Doctrine entity
 * @ORM\Entity
 * @ORM\Table(name="sis_user_prescriptions")
 */
class UserPrescription
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id_prescription")
     * @ORM\GeneratedValue
     */
    protected $id;
    /**
     * @ORM\Column(name="presc_medicamento")
     */
    protected $presc_medicamento;
    /**
     * @ORM\Column(name="presc_dosagem")
     */
    protected $presc_dosagem;
    /**
     * @ORM\Column(name="presc_posologia")
     */
    protected $presc_posologia;
    /**
     * @ORM\Column(name="prof_cadastro")
     */
    protected $prof_cadastro;
    /**
     * @ORM\Column(name="dt_cadastro")
     */
    protected $dt_cadastro;

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
    public function getPrescMedicamento()
    {
        return $this->presc_medicamento;
    }

    /**
     * @param mixed $presc_medicamento
     */
    public function setPrescMedicamento($presc_medicamento)
    {
        $this->presc_medicamento = $presc_medicamento;
    }

    /**
     * @return mixed
     */
    public function getPrescDosagem()
    {
        return $this->presc_dosagem;
    }

    /**
     * @param mixed $presc_dosagem
     */
    public function setPrescDosagem($presc_dosagem)
    {
        $this->presc_dosagem = $presc_dosagem;
    }

    /**
     * @return mixed
     */
    public function getPrescPosologia()
    {
        return $this->presc_posologia;
    }

    /**
     * @param mixed $presc_posologia
     */
    public function setPrescPosologia($presc_posologia)
    {
        $this->presc_posologia = $presc_posologia;
    }

    /**
     * @return mixed
     */
    public function getProfCadastro()
    {
        return $this->prof_cadastro;
    }

    /**
     * @param mixed $prof_cadastro
     */
    public function setProfCadastro($prof_cadastro)
    {
        $this->prof_cadastro = $prof_cadastro;
    }

    /**
     * @return mixed
     */
    public function getDtCadastro()
    {
        return $this->dt_cadastro;
    }

    /**
     * @param mixed $dt_cadastro
     */
    public function setDtCadastro($dt_cadastro)
    {
        $this->dt_cadastro = $dt_cadastro;
    }

}