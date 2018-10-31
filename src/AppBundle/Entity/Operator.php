<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 28.01.18
 * Time: 3:02
 */

namespace AppBundle\Entity;


use AppBundle\Service\SoapUserInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OperatorRepository")
 * @ORM\Table(name="operator")
 */
class Operator implements SoapUserInterface
{

    const TECHAUTOPRO_NUMBER = '08489';

    const BEZOPASNOST_NUMBER = '00594';

    const LODAS_NUMBER = '00429';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $fullName;

    /**
     * @ORM\Column(type="string")
     */
    protected $shortName;

    /**
     * @ORM\Column(type="string")
     */
    protected $regNumber;

    /**
     * @ORM\Column(type="string")
     */
    protected $legalAddress;

    /**
     * @ORM\Column(type="string")
     */
    protected $serviceAddress;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $isActive;

    /**
     * @ORM\Column(type="string")
     */
    protected $soapLogin;

    /**
     * @ORM\Column(type="string")
     */
    protected $soapPassword;

    /**
     * @ORM\Column(type="integer")
     */
    protected $type;

    public function getCredentials()
    {
        return [
            'Name' => $this->soapLogin,
            'Password' => $this->soapPassword
        ];
    }

    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
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
    public function getSoapLogin()
    {
        return $this->soapLogin;
    }

    /**
     * @param mixed $soapLogin
     */
    public function setSoapLogin($soapLogin)
    {
        $this->soapLogin = $soapLogin;
    }

    /**
     * @return mixed
     */
    public function getSoapPassword()
    {
        return $this->soapPassword;
    }

    /**
     * @param mixed $soapPassword
     */
    public function setSoapPassword($soapPassword)
    {
        $this->soapPassword = $soapPassword;
    }

    /**
     * @return mixed
     */
    public function getLegalAddress()
    {
        return $this->legalAddress;
    }

    /**
     * @param mixed $legalAddress
     */
    public function setLegalAddress($legalAddress)
    {
        $this->legalAddress = $legalAddress;
    }

    /**
     * @return mixed
     */
    public function getServiceAddress()
    {
        return $this->serviceAddress;
    }

    /**
     * @param mixed $serviceAddress
     */
    public function setServiceAddress($serviceAddress)
    {
        $this->serviceAddress = $serviceAddress;
    }

    /**
     * @return mixed
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * @param mixed $fullName
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;
    }

    /**
     * @return mixed
     */
    public function getShortName()
    {
        return $this->shortName;
    }

    /**
     * @param mixed $shortName
     */
    public function setShortName($shortName)
    {
        $this->shortName = $shortName;
    }

    /**
     * @return mixed
     */
    public function getRegNumber()
    {
        return $this->regNumber;
    }

    /**
     * @param mixed $regNumber
     */
    public function setRegNumber($regNumber)
    {
        $this->regNumber = $regNumber;
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