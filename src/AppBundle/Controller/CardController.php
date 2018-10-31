<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 27.01.18
 * Time: 23:16
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Card;
use AppBundle\Entity\CarMark;
use AppBundle\Entity\CarModel;
use AppBundle\Entity\Category;
use AppBundle\Entity\Expert;
use AppBundle\Entity\Limit;
use AppBundle\Entity\Operator;
use AppBundle\Entity\OperatorCategory;
use AppBundle\Entity\User;
use AppBundle\Formatter\CardFormatter;
use AppBundle\Service\Soap;
use AppBundle\Service\SoapService;
use AppBundle\Service\SoapTimeOutException;
use AppBundle\Service\SoapUserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Constraints\DateTime;

class CardController extends Controller
{

    /**
     * @Route("/api/cards", name="card_cards")
     * @Method("GET")
     */
    public function cardsAction(Request $request, CardFormatter $formatter)
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'no access');

        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('security.token_storage')->getToken()->getUser();

        $userId = $request->query->get('userId') ?? null;
        $dateFrom = $request->query->get('dateFrom') ?? null;
        $dateTill = $request->query->get('dateTill') ?? null;
        $limit = $request->query->get('limit') ?? 10;
        $offset = $request->query->get('offset') ?? 0;

        $result = $this->getDoctrine()->getRepository(Card::class)->findCardsFiltered($currentUser->getId(), $userId, $dateFrom, $dateTill, $limit, $offset);
        $cards = $formatter->cardList($result['cards']);

        $response = new JsonResponse();
        $response->headers->set('Access-Control-Allow-Origin', '*');

        $response->setContent(json_encode([
            'data' => [
                'countTotal' => $result['countTotal'],
                'cards' => $cards
            ]
        ]));

        return $response;
    }

    /**
     * @Route("/api/stat", name="card_stat")
     */
    public function statAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'no access');

        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('security.token_storage')->getToken()->getUser();

        $userId = $request->query->get('userId') ?? null;
        $dateFrom = $request->query->get('dateFrom') ?? null;
        $dateTill = $request->query->get('dateTill') ?? null;

        $stat = $this->getDoctrine()->getRepository(Card::class)->findStat($currentUser->getId(), $userId, $dateFrom, $dateTill);

        $response = new JsonResponse();
        $response->headers->set('Access-Control-Allow-Origin', '*');

        $response->setContent(json_encode([
            'data' => [
                'stat' => $stat
            ]
        ]));

        return $response;
    }

    /**
     * @return JsonResponse
     * @Route("/api/operatorsCategories")
     */
    public function operatorsCategoriesAction()
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'no access');

        $operatorsCategories = $this->getDoctrine()->getRepository(Operator::class)->findOperatorsCategories();

        $response = new JsonResponse();
        $response->headers->set('Access-Control-Allow-Origin', '*');

        $response->setContent(json_encode([
            'success' => true,
            'data' => [
                'operatorsCategories' => $operatorsCategories
            ]
        ]));

        return $response;
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     * @Route("/api/cards/{id}")
     */
    public function getCardAction(Request $request, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'no access');

        $response = new JsonResponse();

        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Method', 'GET, PUT, POST, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type');

        /**
         * @var Card $card
         */
        $card = $this->getDoctrine()->getRepository(Card::class)->find($id);

        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('security.token_storage')->getToken()->getUser();

        if (!$card) {
            $response->setStatusCode(404);

            $response->setContent(json_encode([
                'success' => false,
                'message' => 'Card not found'
            ]));
        }

        $data['id'] = $card->getId();
        $data['user'] = $card->getUser()->getId();
        $data['propertyType'] = $card->getPropertyType();
        $data['documentType'] = $card->getDocumentType();
        $data['documentNumber'] = $card->getDocumentNumber();
        $data['documentSeries'] = $card->getDocumentSeries();
        $data['legalName'] = $card->getLegalName();
        $data['ownerLastName'] = $card->getOwnerLastName();
        $data['ownerFirstName'] = $card->getOwnerFirstName();
        $data['ownerMiddleName'] = $card->getOwnerMiddleName();
        $data['documentOrganization'] = $card->getDocumentOrganization();
        $data['documentDate'] = $card->getDocumentDate();
        $data['registrationNumber'] = $card->getRegistrationNumber();
        $data['vin'] = $card->getVin();

        $data['carMark'] = [
            'id' => $card->getCarMark()->getIdCarMark(),
            'name' => $card->getCarMark()->getName()
        ];

        $data['carModel'] = [
            'id' => $card->getCarModel()->getIdCarModel(),
            'name' => $card->getCarModel()->getName()
        ];

        $data['vehicleCategory'] = $card->getVehicleCategory()->getId();
        $data['carYear'] = $card->getCarYear();
        $data['bodyNumber'] = $card->getBodyNumber();
        $data['frameNumber'] = $card->getFrameNumber();
        $data['maxMass'] = $card->getMaxMass();
        $data['emptyMass'] = $card->getEmptyMass();
        $data['kilometres'] = $card->getKilometres();
        $data['tyres'] = $card->getTyres();
        $data['operator'] = $card->getOperator()->getId();
        $data['fuelType'] = $card->getFuelType();
        $data['brakingSystem'] = $card->getBrakingSystem();
        $data['note'] = $card->getNote();
        $data['comment'] = $card->getComment();

        $response->setStatusCode(200);
        $response->setContent(json_encode([
            'success' => true,
            'data' => [
                'card' => $data
            ]
        ]));

        return $response;
    }

    /**
     * @param Request $request
     * @Route("/api/cards", methods={"POST", "OPTIONS"})
     * @return Response $response
     */
    public function createCardAction(Request $request)
    {

        /**
         * После приема и валидации клиентских данных,
         * сохраняем их в БД и отправляем в очередь RabbitMQ на отправку в базу ЕАИСТО и обновление
         * нашей базы. Затем клиент смотрит, есть ли карты, находящиеся в процессе обработки и каждые
         * 3 секунды делает запрос по ним на проверку. Если обработка на сервере завершена,
         * обновляем данные по этим картам в UI
         */

        $this->denyAccessUnlessGranted('ROLE_USER', null, 'no access');

        $data = json_decode($request->getContent(), true);

        $response = new JsonResponse();

        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Method', 'GET, PUT, POST, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type');
        //$response->headers->set('Accept', 'application/json');

        if ($request->isMethod('OPTIONS')) {
            $response->setStatusCode(200);
            $response->setContent(json_encode([
                'success' => true
            ]));

            return $response;
        }

        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('security.token_storage')->getToken()->getUser();

        if (!$currentUser->getisActive()) {
            $response->setContent(json_encode([
                'success' => false,
                'message' => 'Ваша учетная запись заблокирована!',
                'data' => [
                    'errors' => [
                        'alert' => [

                        ]
                    ],
                ]
            ]));

            return $response;
        }

        if ($errors = $this->validateCard($data)) {
            $response->setContent(json_encode([
                'success' => false,
                'message' => 'Некорректно заполнены некоторые поля. Пожалуйста, проверьте',
                'data' => [
                    'errors' => $errors
                ]
            ]));

            return $response;
        }

        if (!empty($data['id'])) {
            $entity = $this->getDoctrine()->getRepository(Card::class)->find($data['id']);

            $entity->setUpdatedAt(time());
        } else {
            $entity = new Card();

            $entity->setCreatedAt(time());
            $entity->setUser($currentUser);
        }

        $validTill = (new \DateTime())->modify('+24 months')->getTimestamp();

        if (date('Y') - $data['carYear'] >= 7) {
            $validTill = (new \DateTime())->modify('+12 months')->getTimestamp();
        }

        /**
         * @var Category $vehicleCategory
         */
        $vehicleCategory = $this->getDoctrine()->getRepository(Category::class)->find($data['vehicleCategory']);

        if ($vehicleCategory->getLetter() == 'C') {
            $validTill = (new \DateTime())->modify('+12 months')->getTimestamp();
        }

        if ($vehicleCategory->getLetter() == 'D') {
            $validTill = (new \DateTime())->modify('+6 months')->getTimestamp();
        }

        if ($vehicleCategory->getLetter() == 'E') {
            $validTill = (new \DateTime())->modify('+12 months')->getTimestamp();
        }

        if ($data['note'] == 'Исп. для опасных грузов') {
            $validTill = (new \DateTime())->modify('+6 months')->getTimestamp();
        }

        if ($data['note'] == 'Исп. в качестве такси') {
            $validTill = (new \DateTime())->modify('+6 months')->getTimestamp();
        }

        if ($data['note'] == 'Исп. для маршрутных перевозок') {
            $validTill = (new \DateTime())->modify('+6 months')->getTimestamp();
        }

        if ($data['note'] == 'Исп. для учебной езды' && $vehicleCategory->getLetter() != 'D') {
            $validTill = (new \DateTime())->modify('+12 months')->getTimestamp();
        }

        if (!$data['carMark']) {
            $carMark = new CarMark();

            $carMark->setName($data['carMarkName']);
            $carMark->setIdCarType(1);

            $em = $this->getDoctrine()->getManager();

            $em->persist($carMark);
            $em->flush();

        } else {
            $carMark = $this->getDoctrine()->getRepository(CarMark::class)->find($data['carMark']);
        }

        if (!$data['carModel']) {
            $carModel = new CarModel();

            $carModel->setName($data['carModelName']);
            $carModel->setIdCarMark($carMark->getIdCarMark());
            $carModel->setIdCarType(1);

            $em = $this->getDoctrine()->getManager();

            $em->persist($carModel);
            $em->flush();
        } else {
            $carModel = $this->getDoctrine()->getRepository(CarModel::class)->find($data['carModel']);
        }

        /**
         * @var Operator $operator
         */
        $operator = $this->getDoctrine()->getRepository(Operator::class)->find($data['operator']);

        $limits = $this->getDoctrine()->getRepository(Limit::class)->checkLimits();

        foreach ($limits as $limit) {
            if ($limit['operatorId'] == $operator->getId()) {
                if (!$limit['available']) {
                    $response->setContent(json_encode([
                        'success' => false,
                        'message' => 'Исчерпан лимит',
                        'data' => [
                            'errors' => []
                        ]
                    ]));

                    return $response;
                }
            }
        }

        $entity->setPropertyType($data['propertyType']);
        $entity->setOwnerFirstName(trim($data['ownerFirstName']));
        $entity->setOwnerLastName(trim($data['ownerLastName']));
        $entity->setOwnerMiddleName(trim($data['ownerMiddleName']));
        $entity->setCardIsSecondary(false);
        $entity->setIsArchive(false);
        $entity->setBodyNumber($data['bodyNumber']);
        $entity->setDateOfDiagnosis(time());
        $entity->setValidTill($validTill);
        $entity->setTestResult(true);
        $entity->setTestType(1);
        $entity->setVehicleCategory($vehicleCategory);
        $entity->setVin($data['vin']);
        $entity->setEmptyMass($data['emptyMass']);
        $entity->setMaxMass($data['maxMass']);
        $entity->setFuelType($data['fuelType']);
        $entity->setBrakingSystem($data['brakingSystem']);
        $entity->setTyres($data['tyres']);
        $entity->setKilometres($data['kilometres']);
        $entity->setCarMark($carMark);
        $entity->setCarModel($carModel);
        $entity->setCarYear($data['carYear']);
        $entity->setDocumentType($data['documentType']);
        $entity->setDocumentSeries($data['documentSeries']);
        $entity->setDocumentNumber($data['documentNumber']);
        $entity->setDocumentOrganization($data['documentOrganization']);
        $entity->setDocumentDate($data['documentDate']);
        $entity->setFrameNumber($data['frameNumber']);
        $entity->setRegistrationNumber($data['registrationNumber']);
        $entity->setOperator($operator);
        //$entity->setExpert(1);
        $entity->setLegalName($data['legalName']);
        $entity->setPropertyType($data['propertyType']);
        //$entity->setEaistoNumber(mt_rand(111111, 555555));
        //$entity->setEaistoDate(time());
        $entity->setStatus(2);
        $entity->setIssuedAt(time());
        $entity->setNote($data['note']);
        $entity->setComment($data['comment']);

        if ($operator->getRegNumber() == '08870') {
            $entity->setExpert(1);
        } elseif ($operator->getRegNumber() == '00429') {
            $currentDay = (new \DateTime())->format('d');

            if ($currentDay % 2 === 0) {
                $entity->setExpert(2);
            } else {
                $entity->setExpert(3);
            }
        } elseif ($operator->getRegNumber() == '08489') {
            $entity->setExpert(4);
        } elseif ($operator->getRegNumber() == '09115') {
            $entity->setExpert(5);
        }

        $cardForEaisto = [
            'Values' => [],
            'Name' => $data['ownerFirstName'],
            'FName' => $data['ownerLastName'],
            'MName' => $data['ownerMiddleName'],
            'RegistrationNumber' => $data['registrationNumber'],
            'DateOfDiagnosis' => (new \DateTime())->setTimestamp(time())->format('d-m-Y'),
            'TestResult' => 'Passed',
            'TestType' => 'Primary',
            //'BodyNumber' => $data['bodyNumber'],
            //'FrameNumber' => $data['frameNumber'],
            'Vehicle' => [
                'Make' => $carMark->getName(),
                'Model' => $carModel->getName()
            ],
            'Form' => [
                'Duplicate' => false,
                'Validity' => (new \DateTime())->setTimestamp($validTill)->format('d-m-Y')
            ],
            'VehicleCategory' => $vehicleCategory->getLetter(),
            'VehicleCategory2' => $vehicleCategory->getCode(),
            //'Vin' => $data['vin'],
            'Year' => $data['carYear'],
            'EmptyMass' => $data['emptyMass'],
            'MaxMass' => $data['maxMass'],
            'BrakingSystem' => $data['brakingSystem'],
            'Tyres' => $data['tyres'],
            'Killometrage' => $data['kilometres'],
            'RegistrationDocument' => [
                'DocumentType' => $data['documentType'] == 1 ? 'RegTalon' : 'PTS',
                'Series' => $data['documentSeries'],
                'Number' => $data['documentNumber'],
                'Organization' => $data['documentOrganization'],
                'Date' => (new \DateTime())->setTimestamp($data['documentDate'])->format('d-m-Y')
            ],
            'Expert' => [
                'Name' => 'Знахуренко',
                'FName' => 'Эдуард',
                'MName' => 'Владиславович'
            ],
            'Operator' => [
                'FullName' => $operator->getFullName(),
                'ShortName' => $operator->getShortName()
            ]
        ];

        if ($vehicleCategory->getLetter() !== 'E') {
            $cardForEaisto['Fuel'] = $data['fuelType'];
        }

        if ($data['vin'] != 'ОТСУТСТВУЕТ' && !empty($data['vin'])) {
            $cardForEaisto['Vin'] = $data['vin'];
        }

        if ($data['bodyNumber'] != 'ОТСУТСТВУЕТ' && !empty($data['bodyNumber'])) {
            $cardForEaisto['BodyNumber'] = $data['bodyNumber'];
        }

        if ($data['frameNumber'] != 'ОТСУТСТВУЕТ' && !empty($data['frameNumber'])) {
            $cardForEaisto['FrameNumber'] = $data['frameNumber'];
        }

        if (empty($data['id'])) {
//            $soapService = new SoapService($operator, 'dev_eaisto', 'expert');
//            $eaistoInfo = $soapService->get('RegisterCard', ['card' => $cardForEaisto]);

            /**
             * @var SoapUserInterface $expert
             */
            $expert = $this->getDoctrine()->getRepository(Expert::class)->find($entity->getExpert());

            try {
                $soapService = new Soap($expert, $this->container, $operator->getRegNumber());
                $eaistoInfo = $soapService->registerCard($entity);

                if (!empty($eaistoInfo->Nomer)) {
                    $entity->setEaistoNumber($eaistoInfo->Nomer);
                    $entity->setEaistoId($eaistoInfo->RegisterCardResult);
                    $entity->setEaistoDate(time());
                } else {
                    $response->setStatusCode(200);
                    $response->setContent(json_encode([
                        'success' => false,
                        'message' => $eaistoInfo->getMessage(),
                        'errors' => []
                    ]));

                    return $response;
                }
            } catch (SoapTimeOutException $e) {
//                $response->setStatusCode(200);
//                $response->setContent(json_encode([
//                    'success' => false,
//                    'message' => 'OUTTA TIME'
//                ]));
//
//                return $response;
            }
        } else {
            $operator = $this->getDoctrine()->getRepository(Operator::class)->find(4);

            $soapService = new Soap($operator, $this->container);

            $eaistoInfo = $soapService->editCard($entity);

            $response->setStatusCode(200);
            $response->setContent(json_encode([
                'success' => false,
                'message' => $eaistoInfo->getMessage()
            ]));

            return $response;
        }

        $em = $this->getDoctrine()->getManager();

        $em->persist($entity);
        $em->flush();

        $response->setStatusCode(200);
        $response->setContent(json_encode([
            'success' => true
        ]));

        return $response;
    }

    /**
     * @param Request $request
     * @Route("/api/check_limits", methods={"GET", "OPTIONS"})
     * @return Response $response
     */
    public function checkLimits(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'no access');

        $response = new JsonResponse();

        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Method', 'GET, PUT, POST, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type');
        //$response->headers->set('Accept', 'application/json');

        if ($request->isMethod('OPTIONS')) {
            $response->setStatusCode(200);
            $response->setContent(json_encode([
                'success' => true
            ]));

            return $response;
        }

        $result = $this->getDoctrine()->getRepository(Limit::class)->checkLimits();

        $response->setStatusCode(200);
        $response->setContent(json_encode([
            'success' => true,
            'result' => $result
        ]));

        return $response;
    }

    private function validateCard($data)
    {
        $errors = [];

        if (!in_array($data['propertyType'], [1, 2])) {
            $errors['propertyType'][] = 'Некорректный тип собственности';
        }

        if (empty($data['ownerFirstName'])) {
            $errors['ownerFirstName'][] = 'Имя должно быть указано';
        }

        if (empty($data['ownerLastName'])) {
            $errors['ownerLastName'][] = 'Фамилия должна быть указано';
        }

        if (empty($data['ownerMiddleName'])) {
            $errors['ownerMiddleName'][] = 'Нажмите "Отсутствует", если отчества нет';
        }

        if (empty($data['bodyNumber'])) {
            $errors['bodyNumber'][] = 'Нажмите "Отсутствует", если номера кузова нет';
        }

        if (empty($data['frameNumber'])) {
            $errors['frameNumber'][] = 'Нажмите "Отсутствует", если номера рамы нет';
        }

        if (empty($data['vehicleCategory'])) {
            $errors['vehicleCategory'][] = 'Категория должна быть указана';
        }

        if (empty($data['vin'])) {
            $errors['vin'][] = 'Нажмите "Отсутствует", если VIN нет';
        }

        if (empty($data['maxMass'])) {
            $errors['maxMass'][] = 'Максимальная масса должна быть указана';
        }

        if (empty($data['emptyMass'])) {
            $errors['emptyMass'][] = 'Масса без нагрузки должна быть указана';
        }

        if (empty($data['brakingSystem'])) {
            $errors['brakingSystem'][] = 'Тормозная система должна быть указана';
        }

        if (empty($data['tyres'])) {
            $errors['tyres'][] = 'Марка шин должна быть указана';
        }

        if (empty($data['kilometres'])) {
            $errors['kilometres'][] = 'Пробег должен быть указан';
        }

        if (empty($data['carMark']) && empty($data['carMarkName'])) {
            $errors['carMark'][] = 'Марка авто должна быть указана';
        }

        if (empty($data['carModel']) & empty($data['carModelName'])) {
            $errors['carModel'][] = 'Модель авто должна быть указана';
        }

        if (empty($data['carYear'])) {
            $errors['carYear'][] = 'Год выпуска должен быть указан';
        }

        if (empty($data['documentType'])) {
            $errors['documentType'][] = 'Тип должен быть указан';
        }

        if (empty($data['documentSeries'])) {
            $errors['documentSeries'][] = 'Серия должна быть указана';
        }

        if (empty($data['documentNumber'])) {
            $errors['documentNumber'][] = 'Номер должен быть указан';
        }

        if (empty($data['documentOrganization'])) {
            $errors['documentOrganization'][] = 'Организация должна быть указана';
        }

        if (empty($data['documentDate'])) {
            $errors['documentDate'][] = 'Дата выдачи должна быть указана';
        }

        if ($data['documentDate'] > time()) {
            $errors['documentDate'][] = 'Дата выдачи документа не может быть больше текущей даты';
        }

        if (empty($data['operator'])) {
            $errors['operator'][] = 'Оператор должен быть указан';
        }

        if (empty($data['registrationNumber'])) {
            $errors['registrationNumber'][] = 'Нажмите "Отсутствует", если Рег. номера рамы нет';
        }

        if (!$this->validateProperCategory($data['operator'], $data['vehicleCategory'])) {
            $errors['vehicleCategory'][] = 'У данного ТО нет прав на создание ДК в данной категории ТС';
        }

        if (!$this->validateProperRegistrationNumber($data['vehicleCategory'], $data['registrationNumber'], $data['unusualRegistrationNumber'])) {
            $errors['registrationNumber'][] = 'Некорректно заполнен регистрационный номер. Поставьте галочку "нестандартый", если хотите указать нетипичный рег. номер';
        }

        if (!$this->validateProperVin($data['vin'])) {
            $errors['vin'][] = 'VIN должен содержать 17 символов в диапазоне A-Z и 0-9, за исключением букв I, O, Q';
        }

        if ($data['maxMass'] < $data['emptyMass']) {
            $errors['maxMass'][] = $errors['emptyMass'][] = 'Масса без нагрузки не может быть больше максимальной массы';
        }

        if ((new \DateTime())->setTimestamp($data['documentDate'])->format('Y') < $data['carYear']) {
            $errors['carYear'][] = $errors['documentDate'][] = 'Дата выдачи документа не может быть меньше даты выпуска автомобиля';
        }

        if (!$this->validateIdentity($data['vin'], $data['bodyNumber'], $data['frameNumber'])) {
            $errors['vin'][] = $errors['bodyNumber'][] = $errors['frameNumber'][] = 'Хотя бы один идентифекационный параметр (vin, номер рамы, номер кузова) должен быть указан';
        }

        if (mb_strlen($data['frameNumber']) < 6) {
            $errors['frameNumber'][] = 'Номер рамы должен состоять хотя бы из 6 символов';
        }

        if (mb_strlen($data['bodyNumber']) < 5) {
            $errors['bodyNumber'][] = 'Номер кузова должен состоять хотя бы из 5 символов';
        }

        if (mb_strlen($data['documentSeries']) != 4 && !$data['unusualDocumentSeries']) {
            $errors['documentSeries'][] = 'Серия должна состоять хотя бы из 4 знаков. Поставьте галочку "нестандартная", если хотите указать нетипичную серию';
        }

        if (mb_strlen($data['documentNumber']) !=6 && !$data['unusualDocumentNumber']) {
            $errors['documentNumber'][] = 'Номер должен состоять хотя бы из 6 знаков. Поставьте галочку "нестандартный", если хотите указать нетипичный номер';
        }

        return $errors;

    }

    private function validateIdentity($vin, $bodyNumber, $frameNumber)
    {
        return !(((empty($vin)) || $vin == 'ОТСУТСТВУЕТ') && (empty($bodyNumber) || $bodyNumber == 'ОТСУТСТВУЕТ') && (empty($frameNumber) || $frameNumber == 'ОТСУТСТВУЕТ'));
    }

    private function validateProperCategory($operatorId, $categoryId)
    {
        $properCategory = false;

        $operator = $this->getDoctrine()->getRepository(Operator::class)->find($operatorId);

        /**
         * @var OperatorCategory[] $allowedCategories
         */
        $allowedCategories = $this->getDoctrine()->getRepository(OperatorCategory::class)->findBy(['operator' => $operator]);

        foreach ($allowedCategories as $allowedCategory) {
            if ($allowedCategory->getCategory()->getId() == $categoryId) {
                $properCategory = true;
            }
        }

        return $properCategory;
    }

    private function validateProperRegistrationNumber($categoryId, $registrationNumber, $unusual = false)
    {
        if ($unusual || $registrationNumber == 'ОТСУТСТВУЕТ') {
            return true;
        }

        $patterns = [
            'A' => '#^[0-9]{4}[А-Я]{2}[0-9]{2,3}$#u',
            'B' => '#^[А-Я]{1}[0-9]{3}[А-Я]{2}[0-9]{2,3}$#u',
            'C' => '#^[А-Я]{1}[0-9]{3}[А-Я]{2}[0-9]{2,3}$#u',
            'D' => '#^[А-Я]{2}[0-9]{3}[0-9]{2,3}$#u',
            'E' => '#^[А-Я]{2}[0-9]{4}[0-9]{2,3}$#u'
        ];

        /**
         * @var Category $category
         */
        $category = $this->getDoctrine()->getRepository(Category::class)->find($categoryId);
        $categoryLetter = $category->getLetter();

        $properRegistrationNumber = preg_match($patterns[$categoryLetter], $registrationNumber);

        return $properRegistrationNumber;
    }

    private function validateProperVin($vin)
    {
        if ($vin == 'ОТСУТСТВУЕТ') {
            return true;
        }

        return preg_match('#^[0123456789ABCDEFGHJKLMNPRSTUVWXYZ]{17}$#', $vin);
    }

}