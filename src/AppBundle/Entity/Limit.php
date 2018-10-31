<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 04.02.18
 * Time: 9:40
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LimitRepository")
 * @ORM\Table(name="limits")
 */
class Limit
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\Column(type="json_array")
     */
    protected $workingHours;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $limitation;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $isActive;

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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getUser() : User
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }



    /**
     * @return mixed
     */
    public function getWorkingHours()
    {
        return $this->workingHours;
    }

    /**
     * @param mixed $workingHours
     */
    public function setWorkingHours($workingHours)
    {
        $this->workingHours = $workingHours;
    }

    /**
     * @return mixed
     */
    public function getLimitation()
    {
        return $this->limitation;
    }

    /**
     * @param mixed $limitation
     */
    public function setLimitation($limitation)
    {
        $this->limitation = $limitation;
    }

    /**
     * @return mixed
     */
    public function getisActive()
    {
        return $this->isActive;
    }

    /**
     * @param mixed $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }



}