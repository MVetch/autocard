<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cities
 *
 * @ORM\Table(name="cities")
 * @ORM\Entity
 */
class Cities
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="ru_type", type="string", length=50, nullable=false)
     */
    private $ruType;

    /**
     * @var string
     *
     * @ORM\Column(name="ru_path", type="string", length=50, nullable=false)
     */
    private $ruPath;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

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
     * @return string
     */
    public function getRuType(): string
    {
        return $this->ruType;
    }

    /**
     * @param string $ruType
     */
    public function setRuType(string $ruType)
    {
        $this->ruType = $ruType;
    }

    /**
     * @return string
     */
    public function getRuPath(): string
    {
        return $this->ruPath;
    }

    /**
     * @param string $ruPath
     */
    public function setRuPath(string $ruPath)
    {
        $this->ruPath = $ruPath;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

}

