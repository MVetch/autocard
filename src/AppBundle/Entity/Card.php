<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 28.01.18
 * Time: 1:59
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CardRepository")
 * @ORM\Table(name="card")
 */
class Card implements DCInterface
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\Column(type="smallint")
     */
    protected $propertyType;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $legalName;

    /**
     * @ORM\Column(type="string")
     */
    protected $ownerFirstName;

    /**
     * @ORM\Column(type="string")
     */
    protected $ownerLastName;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $ownerMiddleName;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $cardIsSecondary;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $cardIsDuplicateFor;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $isArchive;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $bodyNumber;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $dateOfDiagnosis;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $comment;

    /**
     * @ORM\Column(type="integer")
     */
    protected $validTill;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $note;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $registrationNumber;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $testResult;

    /**
     * @ORM\Column(type="smallint")
     */
    protected $testType;

    /**
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     * @var Category $vehicleCategory
     */
    protected $vehicleCategory;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $vin;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $frameNumber;

    /**
     * @ORM\Column(type="integer")
     */
    protected $emptyMass;

    /**
     * @ORM\Column(type="integer")
     */
    protected $maxMass;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $fuelType;

    /**
     * @ORM\Column(type="string")
     */
    protected $brakingSystem;

    /**
     * @ORM\Column(type="string")
     */
    protected $tyres;

    /**
     * @ORM\Column(type="integer")
     */
    protected $kilometres;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $dateOfRetest;

    /**
     * @ORM\ManyToOne(targetEntity="CarMark")
     * @ORM\JoinColumn(name="car_mark_id", referencedColumnName="id_car_mark")
     * @var CarMark $carMark
     */
    protected $carMark;

    /**
     * @ORM\ManyToOne(targetEntity="CarModel")
     * @ORM\JoinColumn(name="car_model_id", referencedColumnName="id_car_model")
     * @var CarModel $carModel
     */
    protected $carModel;

    /**
     * @ORM\Column(type="integer")
     */
    protected $carYear;

    /**
     * @ORM\Column(type="smallint")
     */
    protected $documentType;

    /**
     * @ORM\Column(type="string")
     */
    protected $documentSeries;

    /**
     * @ORM\Column(type="string")
     */
    protected $documentNumber;

    /**
     * @ORM\Column(type="string")
     */
    protected $documentOrganization;

    /**
     * @ORM\Column(type="integer")
     */
    protected $documentDate;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $documentIsForeign;

    /**
     * @ORM\ManyToOne(targetEntity="Operator")
     * @ORM\JoinColumn(name="operator_id", referencedColumnName="id")
     * @var Operator $operator
     */
    protected $operator;

    /**
     * @ORM\Column(type="string")
     */
    protected $expert;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $eaistoNumber;

    /**
     * @ORM\Column(type="integer",  nullable=true)
     */
    protected $eaistoDate;

    /**
     * @ORM\Column(type="smallint",  nullable=true)
     */
    protected $status;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $eaistoId;

    /**
     * @ORM\Column(type="integer")
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $issuedAt;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $updatedAt;

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
    public function getEaistoId()
    {
        return $this->eaistoId;
    }

    /**
     * @param mixed $eaistoId
     */
    public function setEaistoId($eaistoId)
    {
        $this->eaistoId = $eaistoId;
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
     * @return mixed
     */
    public function getUser() : User
    {
        return $this->user;
    }

    /**
     * @return mixed
     */
    public function getPropertyType()
    {
        return $this->propertyType;
    }

    /**
     * @return mixed
     */
    public function getLegalName()
    {
        return $this->legalName;
    }

    /**
     * @param mixed $legalName
     */
    public function setLegalName($legalName)
    {
        $this->legalName = $legalName;
    }

    /**
     * @param mixed $propertyType
     */
    public function setPropertyType($propertyType)
    {
        $this->propertyType = $propertyType;
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
    public function getCardIsSecondary()
    {
        return $this->cardIsSecondary;
    }

    /**
     * @param mixed $cardIsSecondary
     */
    public function setCardIsSecondary($cardIsSecondary)
    {
        $this->cardIsSecondary = $cardIsSecondary;
    }

    /**
     * @return mixed
     */
    public function getOwnerFirstName()
    {
        return $this->ownerFirstName;
    }

    /**
     * @param mixed $ownerFirstName
     */
    public function setOwnerFirstName($ownerFirstName)
    {
        $this->ownerFirstName = $ownerFirstName;
    }

    /**
     * @return mixed
     */
    public function getOwnerLastName()
    {
        return $this->ownerLastName;
    }

    /**
     * @param mixed $ownerLastName
     */
    public function setOwnerLastName($ownerLastName)
    {
        $this->ownerLastName = $ownerLastName;
    }

    /**
     * @return mixed
     */
    public function getOwnerMiddleName()
    {
        return $this->ownerMiddleName;
    }

    /**
     * @param mixed $ownerMiddleName
     */
    public function setOwnerMiddleName($ownerMiddleName)
    {
        $this->ownerMiddleName = $ownerMiddleName;
    }

    /**
     * @return mixed
     */
    public function getCardIsDuplicateFor()
    {
        return $this->cardIsDuplicateFor;
    }

    /**
     * @param mixed $cardIsDuplicateFor
     */
    public function setCardIsDuplicateFor($cardIsDuplicateFor)
    {
        $this->cardIsDuplicateFor = $cardIsDuplicateFor;
    }

    /**
     * @return mixed
     */
    public function getisArchive()
    {
        return $this->isArchive;
    }

    /**
     * @param mixed $isArchive
     */
    public function setIsArchive($isArchive)
    {
        $this->isArchive = $isArchive;
    }

    /**
     * @return mixed
     */
    public function getBodyNumber()
    {
        return $this->bodyNumber;
    }

    /**
     * @param mixed $bodyNumber
     */
    public function setBodyNumber($bodyNumber)
    {
        $this->bodyNumber = $bodyNumber;
    }

    /**
     * @return mixed
     */
    public function getDateOfDiagnosis()
    {
        return $this->dateOfDiagnosis;
    }

    /**
     * @param mixed $dateOfDiagnosis
     */
    public function setDateOfDiagnosis($dateOfDiagnosis)
    {
        $this->dateOfDiagnosis = $dateOfDiagnosis;
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param mixed $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return mixed
     */
    public function getValidTill()
    {
        return $this->validTill;
    }

    /**
     * @param mixed $validTill
     */
    public function setValidTill($validTill)
    {
        $this->validTill = $validTill;
    }

    /**
     * @return mixed
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param mixed $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

    /**
     * @return mixed
     */
    public function getRegistrationNumber()
    {
        return $this->registrationNumber;
    }

    /**
     * @param mixed $registrationNumber
     */
    public function setRegistrationNumber($registrationNumber)
    {
        $this->registrationNumber = $registrationNumber;
    }

    /**
     * @return mixed
     */
    public function getTestResult()
    {
        return $this->testResult;
    }

    /**
     * @param mixed $testResult
     */
    public function setTestResult($testResult)
    {
        $this->testResult = $testResult;
    }

    /**
     * @return mixed
     */
    public function getTestType()
    {
        return $this->testType;
    }

    /**
     * @param mixed $testType
     */
    public function setTestType($testType)
    {
        $this->testType = $testType;
    }

    /**
     * @return mixed
     */
    public function getVehicleCategory() : Category
    {
        return $this->vehicleCategory;
    }

    /**
     * @param mixed $vehicleCategory
     */
    public function setVehicleCategory($vehicleCategory)
    {
        $this->vehicleCategory = $vehicleCategory;
    }

    /**
     * @return mixed
     */
    public function getVin()
    {
        return $this->vin;
    }

    /**
     * @param mixed $vin
     */
    public function setVin($vin)
    {
        $this->vin = $vin;
    }

    /**
     * @return mixed
     */
    public function getFrameNumber()
    {
        return $this->frameNumber;
    }

    /**
     * @param mixed $frameNumber
     */
    public function setFrameNumber($frameNumber)
    {
        $this->frameNumber = $frameNumber;
    }

    /**
     * @return mixed
     */
    public function getEmptyMass()
    {
        return $this->emptyMass;
    }

    /**
     * @param mixed $emptyMass
     */
    public function setEmptyMass($emptyMass)
    {
        $this->emptyMass = $emptyMass;
    }

    /**
     * @return mixed
     */
    public function getMaxMass()
    {
        return $this->maxMass;
    }

    /**
     * @param mixed $maxMass
     */
    public function setMaxMass($maxMass)
    {
        $this->maxMass = $maxMass;
    }

    /**
     * @return mixed
     */
    public function getFuelType()
    {
        return $this->fuelType;
    }

    /**
     * @param mixed $fuelType
     */
    public function setFuelType($fuelType)
    {
        $this->fuelType = $fuelType;
    }

    /**
     * @return mixed
     */
    public function getBrakingSystem()
    {
        return $this->brakingSystem;
    }

    /**
     * @param mixed $brakingSystem
     */
    public function setBrakingSystem($brakingSystem)
    {
        $this->brakingSystem = $brakingSystem;
    }

    /**
     * @return mixed
     */
    public function getTyres()
    {
        return $this->tyres;
    }

    /**
     * @param mixed $tyres
     */
    public function setTyres($tyres)
    {
        $this->tyres = $tyres;
    }

    /**
     * @return mixed
     */
    public function getKilometres()
    {
        return $this->kilometres;
    }

    /**
     * @param mixed $kilometres
     */
    public function setKilometres($kilometres)
    {
        $this->kilometres = $kilometres;
    }

    /**
     * @return mixed
     */
    public function getDateOfRetest()
    {
        return $this->dateOfRetest;
    }

    /**
     * @param mixed $dateOfRetest
     */
    public function setDateOfRetest($dateOfRetest)
    {
        $this->dateOfRetest = $dateOfRetest;
    }

    /**
     * @return mixed
     */
    public function getCarMark() : CarMark
    {
        return $this->carMark;
    }

    /**
     * @param mixed $carMark
     */
    public function setCarMark($carMark)
    {
        $this->carMark = $carMark;
    }

    /**
     * @return mixed
     */
    public function getCarModel() : CarModel
    {
        return $this->carModel;
    }

    /**
     * @param mixed $carModel
     */
    public function setCarModel($carModel)
    {
        $this->carModel = $carModel;
    }

    /**
     * @return mixed
     */
    public function getCarYear()
    {
        return $this->carYear;
    }

    /**
     * @param mixed $carYear
     */
    public function setCarYear($carYear)
    {
        $this->carYear = $carYear;
    }

    /**
     * @return mixed
     */
    public function getDocumentType()
    {
        return $this->documentType;
    }

    /**
     * @param mixed $documentType
     */
    public function setDocumentType($documentType)
    {
        $this->documentType = $documentType;
    }

    /**
     * @return mixed
     */
    public function getDocumentSeries()
    {
        return $this->documentSeries;
    }

    /**
     * @param mixed $documentSeries
     */
    public function setDocumentSeries($documentSeries)
    {
        $this->documentSeries = $documentSeries;
    }

    /**
     * @return mixed
     */
    public function getDocumentNumber()
    {
        return $this->documentNumber;
    }

    /**
     * @param mixed $documentNumber
     */
    public function setDocumentNumber($documentNumber)
    {
        $this->documentNumber = $documentNumber;
    }

    /**
     * @return mixed
     */
    public function getDocumentOrganization()
    {
        return $this->documentOrganization;
    }

    /**
     * @param mixed $documentOrganization
     */
    public function setDocumentOrganization($documentOrganization)
    {
        $this->documentOrganization = $documentOrganization;
    }

    /**
     * @return mixed
     */
    public function getDocumentDate()
    {
        return $this->documentDate;
    }

    /**
     * @param mixed $documentDate
     */
    public function setDocumentDate($documentDate)
    {
        $this->documentDate = $documentDate;
    }

    /**
     * @return mixed
     */
    public function getDocumentIsForeign()
    {
        return $this->documentIsForeign;
    }

    /**
     * @param mixed $documentIsForeign
     */
    public function setDocumentIsForeign($documentIsForeign)
    {
        $this->documentIsForeign = $documentIsForeign;
    }

    /**
     * @return mixed
     */
    public function getOperator() : Operator
    {
        return $this->operator;
    }

    /**
     * @param mixed $operator
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;
    }

    /**
     * @return mixed
     */
    public function getExpert()
    {
        return $this->expert;
    }

    /**
     * @param mixed $expert
     */
    public function setExpert($expert)
    {
        $this->expert = $expert;
    }

    /**
     * @return mixed
     */
    public function getEaistoNumber()
    {
        return $this->eaistoNumber;
    }

    /**
     * @param mixed $eaistoNumber
     */
    public function setEaistoNumber($eaistoNumber)
    {
        $this->eaistoNumber = $eaistoNumber;
    }

    /**
     * @return mixed
     */
    public function getEaistoDate()
    {
        return $this->eaistoDate;
    }

    /**
     * @param mixed $eaistoDate
     */
    public function setEaistoDate($eaistoDate)
    {
        $this->eaistoDate = $eaistoDate;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
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

    /**
     * @return mixed
     */
    public function getIssuedAt()
    {
        return $this->issuedAt;
    }

    /**
     * @param mixed $issuedAt
     */
    public function setIssuedAt($issuedAt)
    {
        $this->issuedAt = $issuedAt;
    }

    public function toArray()
    {
        $data = [
            'card' => [
                'Values' => [],
                'Name' => $this->ownerLastName,
                'FName' => $this->ownerFirstName,
                'MName' => $this->ownerMiddleName,
                'RegistrationNumber' => $this->registrationNumber,
                'DateOfDiagnosis' => (new \DateTime())->setTimestamp($this->dateOfDiagnosis)->format('d-m-Y'),
                'TestResult' => 'Passed',
                'TestType' => 'Primary',
                'BodyNumber' => $this->bodyNumber,
                'FrameNumber' => $this->frameNumber,
                'Vehicle' => [
                    'Make' => $this->carMark->getName(),
                    'Model' => $this->carModel->getName()
                ],
                'Form' => [
                    'Duplicate' => false,
                    'Validity' => (new \DateTime())->setTimestamp($this->validTill)->format('d-m-Y')
                ],
                'VehicleCategory' => $this->vehicleCategory->getLetter(),
                'VehicleCategory2' => $this->vehicleCategory->getCode(),
                //'Vin' => $this->vin,
                'Year' => $this->carYear,
                'EmptyMass' => $this->emptyMass,
                'MaxMass' => $this->maxMass,
                'BrakingSystem' => $this->brakingSystem,
                'Tyres' => $this->tyres,
                'Killometrage' => $this->kilometres,
                'RegistrationDocument' => [
                    'DocumentType' => $this->documentType == 1 ? 'RegTalon' : 'PTS',
                    'Series' => $this->documentSeries,
                    'Number' => $this->documentNumber,
                    'Organization' => $this->documentOrganization,
                    'Date' => (new \DateTime())->setTimestamp($this->documentDate)->format('d-m-Y')
                ],
                'Expert' => [
                    'Name' => 'Знахуренко',
                    'FName' => 'Эдуард',
                    'MName' => 'Владиславович'
                ],
                'Operator' => [
                    'FullName' => $this->operator->getFullName(),
                    'ShortName' => $this->operator->getShortName()
                ]
            ]
        ];

        if ($this->vehicleCategory->getLetter() !== 'E') {
            $data['card']['Fuel'] = $this->fuelType;
        }

        if ($this->vin != 'ОТСУТСТВУЕТ' && !empty($this->vin)) {
            $data['card']['Vin'] = $this->vin;
        }

        return $data;
    }


}