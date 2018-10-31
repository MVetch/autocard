<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 05.02.18
 * Time: 17:02
 */

namespace AppBundle\Service\PDFService;


use AppBundle\Entity\Card;
use AppBundle\Entity\DCInterface;
use Symfony\Component\Validator\Constraints\DateTime;

class Generator extends FPDF
{

    const LODAS_COMMON = 'lodas';

    const LODAS_OWN_PRINT = 'lodas_pechat';

    const LODAS_UNIVERSAL_PRINT = 'lodas_u_pechat';

    const TECHAUTOPRO_COMMON = 'techautopro';

    const TECHAUTOPRO_OWN_PRINT = 'techautopro_pechat';

    const TECHAUTOPRO_UNIVERSAL_PRINT = 'techautopro_u_pechat';

    const BEZOPASNOST_COMMON = 'bezopasnost';

    const BEZOPASNOST_OWN_PRINT = 'bezopasnost_pechat';

    const BEZOPASNOST_UNIVERSAL_PRINT = 'bezopasnost_u_pechat';

    const ISTMOBIL_COMMON = 'istmobil';

    const ISTMOBIL_OWN_PRINT = 'istmobil_pechat';

    const ISTMOBIL_UNIVERSAL_PRINT = 'istmobil_u_pechat';

    const TECHEXPERT_COMMON = 'techautoexpert';

    const TECHEXPERT_OWN_PRINT = 'techautoexpert_pechat';

    const TECHEXPERT_UNIVERSAL_PRINT = 'techautoexpert_u_pechat';

    const TYPE_COMMON = 1;

    const TYPE_OWN_PRINT = 2;

    const TYPE_UNIVERSAL_PRINT = 3;

    public $printForm;

    protected $card;

    protected $info;

    public function __construct(DCInterface $card, $info)
    {
        parent::__construct();

        $this->card = $card;
        $this->info = $info;
    }

    public function createFirstPage()
    {
        $this->Image(__DIR__ . "/forms/{$this->printForm}_1.png",0,0, 210);

        $this->AddFont('Arial', null, 'arial.php');
        $this->AddFont('Arial', 'B', 'arial_bold.php');

        $this->SetFont('Arial',null,10);

        /**
         * EAISTO NUMBER IN TITLE
         */

        $eaistoNumber = $this->card->getEaistoNumber();

        if ($eaistoNumber && strlen($eaistoNumber) === 15) {
            $this->SetXY(25, 20);
            $this->Cell(1, 1, $eaistoNumber[0], 0);

            $this->SetXY(29, 20);
            $this->Cell(1, 1, $eaistoNumber[1], 0);

            $this->SetXY(33, 20);
            $this->Cell(1, 1, $eaistoNumber[2], 0);

            $this->SetXY(37.5, 20);
            $this->Cell(1, 1, $eaistoNumber[3], 0);

            $this->SetXY(41.5, 20);
            $this->Cell(1, 1, $eaistoNumber[4], 0);

            $this->SetXY(45.5, 20);
            $this->Cell(1, 1, $eaistoNumber[5], 0);

            $this->SetXY(49.5, 20);
            $this->Cell(1, 1, $eaistoNumber[6], 0);

            $this->SetXY(53.5, 20);
            $this->Cell(1, 1, $eaistoNumber[7], 0);

            $this->SetXY(57.5, 20);
            $this->Cell(1, 1, $eaistoNumber[8], 0);

            $this->SetXY(61.8, 20);
            $this->Cell(1, 1, $eaistoNumber[9], 0);

            $this->SetXY(65.8, 20);
            $this->Cell(1, 1, $eaistoNumber[10], 0);

            $this->SetXY(70, 20);
            $this->Cell(1, 1, $eaistoNumber[11], 0);

            $this->SetXY(74, 20);
            $this->Cell(1, 1, $eaistoNumber[12], 0);

            $this->SetXY(78, 20);
            $this->Cell(1, 1, $eaistoNumber[13], 0);

            $this->SetXY(82, 20);
            $this->Cell(1, 1, $eaistoNumber[14], 0);
        }

        /**
         * EAISTO DATE IN TITLE
         */

        $eaistoDate = new \DateTime();
        $eaistoDate->setTimestamp($this->card->getValidTill());

        $day = $eaistoDate->format('d');

        $dayFirst = $day[0];
        $daySecond = $day[1];

        $month = $eaistoDate->format('m');

        $monthFirst = $month[0];
        $monthSecond = $month[1];

        $year = $eaistoDate->format('Y');

        $yearFirst = $year[0];
        $yearSecond = $year[1];
        $yearThird = $year[2];
        $yearFourth = $year[3];

        $this->SetXY(138, 20);
        $this->Cell(1, 1, $dayFirst, 0);

        $this->SetXY(142, 20);
        $this->Cell(1, 1, $daySecond, 0);

        $this->SetXY(146.2, 20);
        $this->Cell(1, 1, $monthFirst, 0);

        $this->SetXY(150.2, 20);
        $this->Cell(1, 1, $monthSecond, 0);

        $this->SetXY(154.2, 20);
        $this->Cell(1, 1, $yearFirst, 0);

        $this->SetXY(158.2, 20);
        $this->Cell(1, 1, $yearSecond, 0);

        $this->SetXY(162.2, 20);
        $this->Cell(1, 1, $yearThird, 0);

        $this->SetXY(166.2, 20);
        $this->Cell(1, 1, $yearFourth, 0);

        /**
         * OPERATOR INFO
         */

        $operator = $this->card->getOperator();

        $fullName = iconv('UTF-8', 'Windows-1251//TRANSLIT', $operator->getFullName());
        $shortName = iconv('UTF-8', 'Windows-1251//TRANSLIT', $operator->getShortName());
        $regNumber = $operator->getRegNumber();
        $legalAddress = iconv('UTF-8', 'Windows-1251//TRANSLIT', $operator->getLegalAddress());

        //$this->SetStyle('B', true);
        $this->SetFont('Arial','B',8.5);

        if ($this->card->getOperator()->getRegNumber() == '00594') {
            $this->SetXY(53.9, 28.2);
            $this->Cell(1, 1, "{$fullName},", 0);

            $this->SetXY(5.7, 31.5);
            $this->Cell(1, 1, "{$shortName}, íîìåð â ðååñòðå {$regNumber}, $legalAddress", 0);
        } else {
            $this->SetXY(53.9, 28.2);
            $this->Cell(1, 1, "{$fullName}, {$shortName}, íîìåð â", 0);

            $this->SetXY(5.7, 31.5);
            $this->Cell(1, 1, "ðååñòðå {$regNumber}, $legalAddress", 0);
        }

        /**
         * OPERATOR ADDRESS
         */

        $serviceAddress = iconv('UTF-8', 'Windows-1251//TRANSLIT', $operator->getServiceAddress());

        $this->SetXY(48.9, 38.4);
        $this->Cell(1, 1, $serviceAddress, 0);

        /**
         * CHECK INFO LEFT
         */

        $isSecondary = $this->card->getCardIsSecondary();

        $registrationNumber = iconv('UTF-8', 'Windows-1251//TRANSLIT', $this->card->getRegistrationNumber())
            ? iconv('UTF-8', 'Windows-1251//TRANSLIT', $this->card->getRegistrationNumber())
            : 'ÎÒÑÓÒÑÒÂÓÅÒ';

        $vin = iconv('UTF-8', 'Windows-1251//TRANSLIT', $this->card->getVin())
            ? iconv('UTF-8', 'Windows-1251//TRANSLIT', $this->card->getVin())
            : 'ÎÒÑÓÒÑÒÂÓÅÒ';

        $frameNumber = iconv('UTF-8', 'Windows-1251//TRANSLIT', $this->card->getFrameNumber())
            ? iconv('UTF-8', 'Windows-1251//TRANSLIT', $this->card->getFrameNumber())
            : 'ÎÒÑÓÒÑÒÂÓÅÒ';

        $bodyNumber = iconv('UTF-8', 'Windows-1251//TRANSLIT', $this->card->getBodyNumber())
            ? iconv('UTF-8', 'Windows-1251//TRANSLIT', $this->card->getBodyNumber())
            : 'ÎÒÑÓÒÑÒÂÓÅÒ';

        if ($this->card->getCarMark()) {
            $carMarkName = $this->card->getCarMark()->getName();
        } else {
            $carMarkName = $this->card->getCarMarkName();
        }

        if ($this->card->getCarModel()) {
            $carModelName = $this->card->getCarModel()->getName();
        } else {
            $carModelName = $this->card->getCarModelName();
        }

        $carMark = iconv('UTF-8', 'Windows-1251//TRANSLIT', $carMarkName);
        $carModel = iconv('UTF-8', 'Windows-1251//TRANSLIT', $carModelName);
        $category = $this->card->getVehicleCategory()->getLetter() . ' (' . $this->card->getVehicleCategory()->getCode() . ')';
        $carYear = $this->card->getCarYear();

        $this->SetXY(55, 45);
        $this->Cell(1, 1, $isSecondary ? 'ÍÅÒ' : 'ÄÀ', 0);

        $this->SetXY(55, 49);
        $this->Cell(1, 1, $registrationNumber ?? 'ÎÒÑÓÒÑÒÂÓÅÒ', 0);

        $this->SetXY(55, 53.3);
        $this->Cell(1, 1, $vin ?? 'ÎÒÑÓÒÑÒÂÓÅÒ', 0);

        $this->SetXY(55, 57.3);
        $this->Cell(1, 1, $frameNumber ?? 'ÎÒÑÓÒÑÒÂÓÅÒ', 0);

        $this->SetXY(55, 61.3);
        $this->Cell(1, 1, $bodyNumber ?? 'ÎÒÑÓÒÑÒÂÓÅÒ', 0);

        /**
         * CHECK INFO RIGHT
         */

        $this->SetXY(154, 45);
        $this->Cell(1, 1, $isSecondary ? 'ÄÀ' : 'ÍÅÒ', 0);

        $this->SetXY(154, 49);
        $this->Cell(1, 1, $carMark, 0);

        $this->SetXY(154, 53.3);
        $this->Cell(1, 1, $carModel, 0);

        $this->SetXY(154, 57.3);
        $this->Cell(1, 1, $category, 0);

        $this->SetXY(154, 61.3);
        $this->Cell(1, 1, $carYear, 0);

        /**
         * DOCUMENT INFO - TYPE
         */

        $this->SetFont('Arial',null,7.5);

//        $this->SetXY(5.7, 67.75);
//        $this->Cell(1, 1, 'ÑÐÒÑ', 0);
//
//        $this->SetXY(19.7, 67.75);
//        $this->Cell(1, 1, 'ÏÒÑ', 0);

        $this->SetFont('Arial','B',8.5);

        $documentType = $this->card->getDocumentType();

        if ($documentType == 1) {
            $this->SetXY(19.7, 66.1);
            $this->Cell(1, 1, '____', 0);
        } else {
            $this->SetXY(5.7, 66.1);
            $this->Cell(1, 1, '_____', 0);
        }


        /**
         * DOCUMENT INFO - IDENTITY
         */

        $documentSeries = iconv('UTF-8', 'Windows-1251//TRANSLIT', $this->card->getDocumentSeries());
        $documentNumber = iconv('UTF-8', 'Windows-1251//TRANSLIT', $this->card->getDocumentNumber());

        $this->SetXY(76, 67.7);
        $this->Cell(1, 1, $documentSeries, 0);

        $this->SetXY(85, 67.7);
        $this->Cell(1, 1, $documentNumber, 0);

        /**
         * DOCUMENT INFO - ORGANIZATION
         */

        $documentOrganization = iconv('UTF-8', 'Windows-1251//TRANSLIT', $this->card->getDocumentOrganization());
        $documentDate = (new \DateTime())->setTimestamp($this->card->getDocumentDate())->format('d.m.Y');

        $this->SetXY(108, 67.7);
        $this->Cell(1, 1, "{$documentOrganization}, {$documentDate}", 0);
    }

    public function createSecondPage()
    {
        $image = __DIR__ . "/forms/{$this->printForm}_2.png";

        if ($this->card->getExpert() == 3) {
            $image = __DIR__ . "/forms/{$this->printForm}_2_2.png";
        }

        $this->Image($image,0,0, 210);

        $this->AddFont('Arial', null, 'arial.php');
        $this->AddFont('Arial', 'B', 'arial_bold.php');

        $this->SetFont('Arial',null,7.5);

        /**
         * NOTE
         */

        if ($this->card->getNote()) {
            $note = iconv('UTF-8', 'Windows-1251//TRANSLIT', $this->card->getNote());
        } else {
            $note = 'Íåò îòìåòîê';
        }


        $this->setXY(6, 196);
        $this->Cell(1, 1, $note);

        /**
         * CAR CHARACTERISTICS LEFT
         */

        $emptyMass = $this->card->getEmptyMass();
//        $fuelType = $this->card->getFuelType();
//        $brakingSystem = $this->card->getBrakingSystem();

        $this->SetFont('Arial','B',7.5);

        $this->setXY(55, 207.3);
        $this->Cell(1, 1, "{$emptyMass} êã");

        $this->setXY(55, 210.5);
        $this->Cell(1, 1, iconv('UTF-8', 'Windows-1251//TRANSLIT', $this->info['fuelType']));

        $this->setXY(55, 213.7);
        $this->Cell(1, 1, iconv('UTF-8', 'Windows-1251//TRANSLIT', $this->info['brakingSystem']));

        /**
         * CAR CHARACTERISTICS RIGHT
         */

        $maxMass = $this->card->getMaxMass();
        $kilometres = $this->card->getKilometres();
        $tyres = iconv('UTF-8', 'Windows-1251//TRANSLIT', $this->card->getTyres());

        $this->setXY(154, 207.3);
        $this->Cell(1, 1, "{$maxMass} êã");

        $this->setXY(154, 210.5);
        $this->Cell(1, 1, "{$kilometres} êì");

        $this->setXY(154, 213.7);
        $this->Cell(1, 1, $tyres);

        /**
         * EAISTO NUMBER
         */

        $eaistoNumber = $this->card->getEaistoNumber();

        $this->SetFont('Arial',null,7.5);

        $this->setXY(24, 219.3);
        $this->Cell(1, 1, $eaistoNumber);

        $testResult = $this->card->getTestResult();

        if (!$testResult) {
            /**
             * TEST RESULT - PASSED
             */

            $this->SetFont('Arial','B',10);

            $this->setXY(159.1, 227.6);
            $this->Cell(1, 1, '______');

            $this->setXY(156, 224.3);
            $this->Cell(1, 1, '_________');
        } else {
            /**
             * TEST RESULT - NOT PASSED
             */

            $this->SetFont('Arial','B', 10);

            $this->setXY(183.1, 227.6);
            $this->Cell(1, 1, '______');

            $this->setXY(178.5, 224.3);
            $this->Cell(1, 1, '___________');
        }

        /**
         * OWNER NAME
         */

        $firstName = iconv('UTF-8', 'Windows-1251//TRANSLIT', $this->card->getOwnerFirstName());
        $lastName = iconv('UTF-8', 'Windows-1251//TRANSLIT', $this->card->getOwnerLastName());
        $middleName = (!$this->card->getOwnerMiddleName()
            || iconv('UTF-8', 'Windows-1251//TRANSLIT', $this->card->getOwnerMiddleName()) == 'ÎÒÑÓÒÑÒÂÓÅÒ')
                ? null
                : iconv('UTF-8', 'Windows-1251//TRANSLIT', $this->card->getOwnerMiddleName());

        $this->SetFont('Arial', null, 8.5);

        $this->setXY(104.5, 236);
        $this->Cell(1, 1, "{$lastName} {$firstName} {$middleName}");

        /**
         * CARD DATE
         */

        $eaistoDate = (new \DateTime())->setTimestamp($this->card->getEaistoDate())->format('d.m.Y');

        $this->SetFont('Arial','B',7.5);

        $this->setXY(55., 250.4);
        $this->Cell(1, 1, $eaistoDate);
    }

}