<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CarModel
 *
 * @ORM\Table(name="car_model", indexes={@ORM\Index(name="name", columns={"name"}), @ORM\Index(name="id_car_mark", columns={"id_car_mark"}), @ORM\Index(name="id_car_type", columns={"id_car_type"})})
 * @ORM\Entity
 */
class CarModel
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_car_mark", type="integer", nullable=false)
     */
    private $idCarMark;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="date_create", type="integer", nullable=true)
     */
    private $dateCreate;

    /**
     * @var integer
     *
     * @ORM\Column(name="date_update", type="integer", nullable=true)
     */
    private $dateUpdate;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_car_type", type="integer", nullable=false)
     */
    private $idCarType;

    /**
     * @var string
     *
     * @ORM\Column(name="name_rus", type="string", length=255, nullable=true)
     */
    private $nameRus;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_car_model", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idCarModel;

    /**
     * @return int
     */
    public function getIdCarMark(): int
    {
        return $this->idCarMark;
    }

    /**
     * @param int $idCarMark
     */
    public function setIdCarMark(int $idCarMark)
    {
        $this->idCarMark = $idCarMark;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getDateCreate(): int
    {
        return $this->dateCreate;
    }

    /**
     * @param int $dateCreate
     */
    public function setDateCreate(int $dateCreate)
    {
        $this->dateCreate = $dateCreate;
    }

    /**
     * @return int
     */
    public function getDateUpdate(): int
    {
        return $this->dateUpdate;
    }

    /**
     * @param int $dateUpdate
     */
    public function setDateUpdate(int $dateUpdate)
    {
        $this->dateUpdate = $dateUpdate;
    }

    /**
     * @return int
     */
    public function getIdCarType(): int
    {
        return $this->idCarType;
    }

    /**
     * @param int $idCarType
     */
    public function setIdCarType(int $idCarType)
    {
        $this->idCarType = $idCarType;
    }

    /**
     * @return string
     */
    public function getNameRus(): string
    {
        return $this->nameRus;
    }

    /**
     * @param string $nameRus
     */
    public function setNameRus(string $nameRus)
    {
        $this->nameRus = $nameRus;
    }

    /**
     * @return int
     */
    public function getIdCarModel(): int
    {
        return $this->idCarModel;
    }

    /**
     * @param int $idCarModel
     */
    public function setIdCarModel(int $idCarModel)
    {
        $this->idCarModel = $idCarModel;
    }


}

