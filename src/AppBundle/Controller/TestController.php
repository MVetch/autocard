<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 28.01.18
 * Time: 3:57
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Card;
use AppBundle\Entity\CarMark;
use AppBundle\Entity\CarModel;
use AppBundle\Entity\Category;
use AppBundle\Entity\FuelType;
use AppBundle\Entity\Operator;
use AppBundle\Entity\User;
use AppBundle\Service\SoapService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;


class TestController extends Controller
{

    /**
     * @Route("/test/test")
     */
    public function testAction()
    {
        $result = $this->getDoctrine()->getRepository(FuelType::class)->findAll();

        $response = new JsonResponse();
        $response->headers->set('Access-Control-Allow-Origin', '*');

        $response->setContent(json_encode([
            'data' => [
                'fuelTypes' => $result
            ]
        ]));

        return $response;

    }

    /**
     * @Route("/test/add_categories")
     */
    public function addCategoriesAction()
    {
        $categories = [
            [
                'id' => 1,
                'letter' => 'A',
                'code' => 'L',
                'description' => 'Мототранспортные средства'
            ],
            [
                'id' => 2,
                'letter' => 'B',
                'code' => 'M1',
                'description' => 'Легковые автомобили'
            ],
            [
                'id' => 3,
                'letter' => 'B',
                'code' => 'N1',
                'description' => 'Грузовые автомобили до 3.5 тонн'
            ],
            [
                'id' => 4,
                'letter' => 'C',
                'code' => 'N2',
                'description' => 'Грузовые автомобили 3.5 - 12 тонна'
            ],
            [
                'id' => 5,
                'letter' => 'C',
                'code' => 'N3',
                'description' => 'Грузовые автомобили свыше 12 тонн'
            ],
            [
                'id' => 6,
                'letter' => 'D',
                'code' => 'M2',
                'description' => 'Автобусы до 5 тонн'
            ],
            [
                'id' => 7,
                'letter' => 'D',
                'code' => 'M3',
                'description' => 'Автобусы свыше 5 тонн'
            ],
            [
                'id' => 8,
                'letter' => 'E',
                'code' => 'O1',
                'description' => 'Прицепы до 0.75 тонн'
            ],
            [
                'id' => 9,
                'letter' => 'E',
                'code' => 'O2',
                'description' => 'Прицепы 0.75 - 3.5 тонн'
            ],
            [
                'id' => 10,
                'letter' => 'E',
                'code' => 'O3',
                'description' => 'Прицепы 3.5 - 12 тонн'
            ],
            [
                'id' => 11,
                'letter' => 'E',
                'code' => 'O4',
                'description' => 'Прицепы свыше 12 тонн'
            ]
        ];

        $em = $this->getDoctrine()->getManager();

        foreach ($categories as $category) {
            $entity = new Category();

            $entity->setLetter($category['letter']);
            $entity->setCode($category['code']);
            $entity->setDescription($category['description']);

            $em->persist($entity);
            $em->flush();
        }
    }

    /**
     * @Route("/test/add_operators")
     */
    public function addOperatorsAction()
    {
        $operators = [
            [
                'fullName' => 'Общество с ограниченной ответственностью "Техавтопро"',
                'shortName' => 'ООО "Техавтопро"',
                'regNumber' => '08489',
                'isActive' => true
            ],
            [
                'fullName' => 'Общество с ограниченной ответственностью "Безопасность движения"',
                'shortName' => 'ООО "Безопасность движения"',
                'regNumber' => '00594',
                'isActive' => true
            ],
            [
                'fullName' => 'Общество с ограниченной ответственностью "Лодас"',
                'shortName' => 'ООО "Лодас"',
                'regNumber' => '00429',
                'isActive' => true
            ]
        ];

        $em = $this->getDoctrine()->getManager();

        foreach ($operators as $operator) {
            $entity = new Operator();

            $entity->setFullName($operator['fullName']);
            $entity->setShortName($operator['shortName']);
            $entity->setIsActive(true);
            $entity->setRegNumber($operator['regNumber']);

            $em->persist($entity);
            $em->flush();
        }
    }

    /**
     * @Route("/test/add_cards")
     */
    public function addCardsAction()
    {

//        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
//        $carMark = $this->getDoctrine()->getRepository(CarMark::class)->findOneBy(['idCarMark' => 18]);
//        $carModels = $this->getDoctrine()->getRepository(CarModel::class)->findBy(['idCarMark' => 18]);
//        $operators = $this->getDoctrine()->getRepository(Operator::class)->findAll();
//        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
//
//        $firstNames = [
//            'АЛЕКСАНДР',
//            'АНАТОЛИЙ',
//            'АРТЕМ',
//            'АНТОН',
//            'ВИКТОР',
//            'ДМИТРИЙ'
//        ];
//
//        $lastNames = [
//            'ИВАНОВ',
//            'ЗОДЧЕНКО',
//            'ДМИТРИЕВ',
//            'КРОКИН',
//            'СЕРГЕЕВ'
//        ];
//
//        $middleNames = [
//            'ИВАНОВИЧ',
//            'ПЕТРОВИЧ',
//            'ДМИТРИЕВИЧ',
//            'АНТОНОВИЧ',
//            'СЕРГЕЕВИЧ'
//        ];
//
//        $minCreatedDate = 1513036800;
//        $maxCreatedDate = time();
//
//        $cards = [];
//
//        for ($i = 0; $i < 27; $i++) {
//            $cards[] = [
//                'user' => $users[4],
//                'propertyType' => 1,
//                'ownerFirstName' => $firstNames[mt_rand(0, count($firstNames) - 1)],
//                'ownerLastName' => $lastNames[mt_rand(0, count($lastNames) - 1)],
//                'ownerMiddleName' => $middleNames[mt_rand(0, count($middleNames) - 1)],
//                'cardIsSecondary' => false,
//                'isArchive' => false,
//                'bodyNumber' => '411AA6544433' . mt_rand(11111, 55555),
//                'dateOfDiagnosis' => mt_rand($minCreatedDate, $maxCreatedDate),
//                'validTill' => mt_rand($minCreatedDate, $maxCreatedDate) + 2 * 365 * 24 * 3600,
//                'testResult' => true,
//                'testType' => 1,
//                'vehicleCategory' => $categories[mt_rand(0, count($categories) - 1)],
//                'vin' => 'WVWZZZ3CZ8E1' . mt_rand(11111, 55555),
//                'emptyMass' => mt_rand(1500, 2000),
//                'maxMass' => mt_rand(2000, 3000),
//                'fuelType' => 'Petrol',
//                'brakingSystem' => 'Hydraulic',
//                'tyres' => 'BFGoodrich',
//                'kilometres' => mt_rand(11111, 55555),
//                'carMark' => $carMark,
//                'carModel' => $carModels[mt_rand(0, count($carModels) - 1)],
//                'carYear' => mt_rand(1996, 2017),
//                'documentType' => mt_rand(1, 2),
//                'documentSeries' => mt_rand(1111, 4444),
//                'documentNumber' => mt_rand(111111, 555555),
//                'documentOrganization' => 'ГИБДД ' . mt_rand(111111, 555555),
//                'documentDate' => 1638316800,
//                'operator' => $operators[mt_rand(0, count($operators) - 1)],
//                'expert' => 'Калинин Сергей Петрович',
//                'eaistoNumber' => '005940041' . mt_rand(111111, 555555),
//                'eaistoDate' => mt_rand($minCreatedDate, $maxCreatedDate) + 2 * 365 * 24 * 3600,
//                'status' => 2,
//                'createdAt' => mt_rand($minCreatedDate, $maxCreatedDate),
//                'issuedAt' => mt_rand($minCreatedDate, $maxCreatedDate) + 2
//            ];
//        }

//        $cards = [
//            [
//                'user' => $users[0],
//                'propertyType' => 1,
//                'ownerFirstName' => 'ИВАН',
//                'ownerLastName' => 'ИВАНОВ',
//                'ownerMiddleName' => 'ИВАНОВИЧ',
//                'cardIsSecondary' => false,
//                'isArchive' => false,
//                'bodyNumber' => 'KNADH411AA6597696',
//                'dateOfDiagnosis' => time(),
//                'validTill' => time() + 2 * 365 * 24 * 3600,
//                'testResult' => true,
//                'testType' => 1,
//                'vehicleCategory' => $categories[1],
//                'vin' => 'WVWZZZ3CZ8E182621',
//                'emptyMass' => 1570,
//                'maxMass' => 2200,
//                'fuelType' => 'Petrol',
//                'brakingSystem' => 'Hydraulic',
//                'tyres' => 'BFGoodrich',
//                'kilometres' => '210355',
//                'carMark' => $carMark,
//                'carModel' => $carModels[0],
//                'carYear' => 2012,
//                'documentType' => 1,
//                'documentSeries' => 4742,
//                'documentNumber' => 369276,
//                'documentOrganization' => 'ГИБДД 1141092',
//                'documentDate' => 1638316800,
//                'operator' => $operators[0],
//                'expert' => 'Калинин Сергей Петрович',
//                'eaistoNumber' => '005940041800791',
//                'eaistoDate' => 1638316800,
//                'status' => 2,
//                'createdAt' => time(),
//                'issuedAt' => time()
//            ]
//        ];

//        $em = $this->getDoctrine()->getManager();
//
//        foreach ($cards as $card) {
//            $entity = new Card();
//
//            $entity->setUser($card['user']);
//            $entity->setPropertyType($card['propertyType']);
//            $entity->setOwnerFirstName($card['ownerFirstName']);
//            $entity->setOwnerLastName($card['ownerLastName']);
//            $entity->setOwnerMiddleName($card['ownerMiddleName']);
//            $entity->setCardIsSecondary($card['cardIsSecondary']);
//            $entity->setIsArchive($card['isArchive']);
//            $entity->setBodyNumber($card['bodyNumber']);
//            $entity->setDateOfDiagnosis($card['dateOfDiagnosis']);
//            $entity->setValidTill($card['validTill']);
//            $entity->setTestResult($card['testResult']);
//            $entity->setTestType($card['testType']);
//            $entity->setVehicleCategory($card['vehicleCategory']);
//            $entity->setVin($card['vin']);
//            $entity->setEmptyMass($card['emptyMass']);
//            $entity->setMaxMass($card['maxMass']);
//            $entity->setFuelType($card['fuelType']);
//            $entity->setBrakingSystem($card['brakingSystem']);
//            $entity->setTyres($card['tyres']);
//            $entity->setKilometres($card['kilometres']);
//            $entity->setCarMark($card['carMark']);
//            $entity->setCarModel($card['carModel']);
//            $entity->setCarYear($card['carYear']);
//            $entity->setDocumentType($card['documentType']);
//            $entity->setDocumentSeries($card['documentSeries']);
//            $entity->setDocumentNumber($card['documentNumber']);
//            $entity->setDocumentOrganization($card['documentOrganization']);
//            $entity->setDocumentDate($card['documentDate']);
//            $entity->setOperator($card['operator']);
//            $entity->setExpert($card['expert']);
//            $entity->setEaistoNumber($card['eaistoNumber']);
//            $entity->setEaistoDate($card['eaistoDate']);
//            $entity->setStatus($card['status']);
//            $entity->setCreatedAt($card['createdAt']);
//            $entity->setIssuedAt($card['issuedAt']);
//
//            $em->persist($entity);
//
//            $em->flush();
//        }

        $cards = [
            [
                'card' => [
                    'Values' => [],
                    'Name' => 'ИВАН',
                    'FName' => 'ИВАНОВ',
                    'MName' => 'ИВАНОВИЧ',
                    'RegistrationNumber' => 'D322DD22Z',
                    'DateOfDiagnosis' => '12-02-2018',
                    'TestResult' => 'Passed',
                    'TestType' => 'Primary',
                    'BodyNumber' => 'AF88898888',
                    'FrameNumber' => '234',
                    'Vehicle' => [
                        'Make' => 'VOLKSWAGEN',
                        'Model' => 'PASSAT'
                    ],
                    'Form' => [
                        'Duplicate' => false,
                        'Validity' => '12-07-2019'
                    ],
                    'VehicleCategory' => 'B',
                    'VehicleCategory2' => 'M1',
                    'Vin' => 'W3W0W234WWD537C59',
                    'Year' => 2012,
                    'EmptyMass' => '1720',
                    'MaxMass' => '2500',
                    'Fuel' => 'Petrol',
                    'BrakingSystem' => 'Hydraulic',
                    'Tyres' => 'BFGoodrich',
                    'Killometrage' => '200554',
                    'RegistrationDocument' => [
                        'DocumentType' => 'RegTalon',
                        'Series' => '33DC',
                        'Number' => '369276',
                        'Organization' => 'ГИБДД 1141092',
                        'Date' => '12-07-2016'
                    ],
                    'Expert' => [
                        'Name' => 'Прохоров',
                        'FName' => 'Валерий',
                        'MName' => 'Андреевич'
                    ],
                    'Operator' => [
                        'FullName' => 'Общество с ограниченной ответственностью "Безопасность движения"',
                        'ShortName' => 'ООО "Безопасность движения"'
                    ]
                ]
            ]
        ];

        //$soapService = $this->container->get('app_bundle.soap_service');

        /**
         * @var Operator $operator
         */
        $operator = $this->getDoctrine()->getRepository(Operator::class)->find(1);
        $soapService = new SoapService($operator, 'dev_eaisto', 'expert');

        $arguments = [];

        $arguments['card'] = $cards[0]['card'];

        $result = $soapService->get('RegisterCard', $arguments);

        dump($result);die();
    }

    public function registerCardAction()
    {

    }

}