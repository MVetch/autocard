<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 28.01.18
 * Time: 2:41
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @ORM\Table(name="users")
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $parentId;

    /**
     * @ORM\Column(type="string", unique=true, nullable=true)
     */
    protected $email;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    protected $login;

    /**
     * @var
     */
    protected $plainPassword;

    /**
     * @ORM\Column(type="string", length=64)
     */
    protected $password;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $apiToken;

    /**
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @ORM\ManyToOne(targetEntity="Cities")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     */
    protected $city;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $isActive;

    /**
     * @ORM\Column(type="integer")
     */
    protected $priceA;

    /**
     * @ORM\Column(type="integer")
     */
    protected $priceB;

    /**
     * @ORM\Column(type="integer")
     */
    protected $priceC;

    /**
     * @ORM\Column(type="integer")
     */
    protected $priceD;

    /**
     * @ORM\Column(type="integer")
     */
    protected $priceE;

    /**
     * @ORM\Column(type="json_array", nullable=true)
     */
    protected $ips;

    /**
     * @ORM\Column(type="integer")
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $updatedAt;

    /**
     * @ORM\Column(type="json_array")
     */
    protected $roles;

    public function __construct()
    {
        $this->isActive = true;
    }

    public function getUsername()
    {
        return $this->login;
    }

    public function getSalt()
    {
        return null;
    }

    /**
     * @return mixed
     */
    public function getIps()
    {
        return $this->ips;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param mixed $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @param mixed $ips
     */
    public function setIps($ips)
    {
        $this->ips = $ips;
    }

    /**
     * @return mixed
     */
    public function getApiToken()
    {
        return $this->apiToken;
    }

    /**
     * @param mixed $apiToken
     */
    public function setApiToken($apiToken)
    {
        $this->apiToken = $apiToken;
    }

    /**
     * @return mixed
     */
    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    /**
     * @param mixed $roles
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    /**
     * @return mixed
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param mixed $plainPassword
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }



    public function eraseCredentials()
    {
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->login,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->login,
            $this->password,
            // see section on salt below
            // $this->salt
            ) = unserialize($serialized);
    }

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
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * @param mixed $parentId
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param mixed $login
     */
    public function setLogin($login)
    {
        $this->login = $login;
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
    public function getCity() :Cities
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city)
    {
        $this->city = $city;
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

    /**
     * @return mixed
     */
    public function getPriceA()
    {
        return $this->priceA;
    }

    /**
     * @param mixed $priceA
     */
    public function setPriceA($priceA)
    {
        $this->priceA = $priceA;
    }

    /**
     * @return mixed
     */
    public function getPriceB()
    {
        return $this->priceB;
    }

    /**
     * @param mixed $priceB
     */
    public function setPriceB($priceB)
    {
        $this->priceB = $priceB;
    }

    /**
     * @return mixed
     */
    public function getPriceC()
    {
        return $this->priceC;
    }

    /**
     * @param mixed $priceC
     */
    public function setPriceC($priceC)
    {
        $this->priceC = $priceC;
    }

    /**
     * @return mixed
     */
    public function getPriceD()
    {
        return $this->priceD;
    }

    /**
     * @param mixed $priceD
     */
    public function setPriceD($priceD)
    {
        $this->priceD = $priceD;
    }

    /**
     * @return mixed
     */
    public function getPriceE()
    {
        return $this->priceE;
    }

    /**
     * @param mixed $priceE
     */
    public function setPriceE($priceE)
    {
        $this->priceE = $priceE;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

}