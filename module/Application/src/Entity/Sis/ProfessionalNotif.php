<?php


namespace Application\Entity\Sis;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="sis_professional_notif")
 */
class ProfessionalNotif
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id_notif")
     */
    protected $id;
    /**
     * @ORM\Column(name="id_professional")
     */
    protected $id_professional;
    /**
     * @ORM\Column(name="notif_motivo")
     */
    protected $notif_motivo;
    /**
     * @ORM\Column(name="dt_inicio")
     */
    protected $dt_inicio;
    /**
     * @ORM\Column(name="dt_fim")
     */
    protected $dt_fim;

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
    public function getNotifMotivo()
    {
        return $this->notif_motivo;
    }

    /**
     * @param mixed $notif_motivo
     */
    public function setNotifMotivo($notif_motivo)
    {
        $this->notif_motivo = $notif_motivo;
    }

    /**
     * @return mixed
     */
    public function getDtInicio()
    {
        return $this->dt_inicio;
    }

    /**
     * @param mixed $dt_inicio
     */
    public function setDtInicio($dt_inicio)
    {
        $this->dt_inicio = $dt_inicio;
    }

    /**
     * @return mixed
     */
    public function getDtFim()
    {
        return $this->dt_fim;
    }

    /**
     * @param mixed $dt_fim
     */
    public function setDtFim($dt_fim)
    {
        $this->dt_fim = $dt_fim;
    }

}