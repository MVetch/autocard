<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 24.09.2018
 * Time: 16:54
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Application;
use AppBundle\Entity\Category;
use AppBundle\Entity\Expert;
use AppBundle\Entity\Limit;
use AppBundle\Entity\Operator;
use AppBundle\Entity\OperatorCategory;
use AppBundle\Entity\User;
use AppBundle\Formatter\CardFormatter;
use AppBundle\Helper\Validator;
use AppBundle\Service\Soap;
use AppBundle\Service\SoapUserInterface;
use AppBundle\Validator\DC;
use AppBundle\Validator\DCValidator;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApplicationController extends Controller
{

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     * @Route("/api/applications/{id}")
     */
    public function getApplicationAction(Request $request, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'no access');

        $response = new JsonResponse();

        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Method', 'GET, PUT, POST, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type');

        /**
         * @var Application $card
         */
        $card = $this->getDoctrine()->getRepository(Application::class)->find($id);

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

        $vehicleCategory = $card->getVehicleCategory();

        $data['id'] = $card->getId();
        //$data['user'] = $card->getUser()->getId();
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
        $data['carMarkName'] = $card->getCarMarkName();
        $data['carModelName'] = $card->getCarModelName();

        if ($vehicleCategory) {
            $data['vehicleCategory'] = $card->getVehicleCategory()->getId();
        }

        $data['carYear'] = $card->getCarYear();
        $data['bodyNumber'] = $card->getBodyNumber();
        $data['frameNumber'] = $card->getFrameNumber();
        $data['maxMass'] = $card->getMaxMass();
        $data['emptyMass'] = $card->getEmptyMass();
        $data['kilometres'] = $card->getKilometres();
        $data['tyres'] = $card->getTyres();
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
     * @Route("/api/applications", methods={"GET", "OPTIONS"})
     * @return Response $response
     */
    public function indexAction(Request $request, CardFormatter $formatter)
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'no access');

        $dateFrom = $request->query->get('dateFrom') ?? null;
        $dateTill = $request->query->get('dateTill') ?? null;
        $limit = $request->query->get('limit') ?? 10;
        $offset = $request->query->get('offset') ?? 0;

        $res = $this->getDoctrine()->getRepository(Application::class)
            ->findApplicationsFiltered($dateFrom, $dateTill, $limit, $offset);

        $applications = $formatter->applicationList($res['data']);

        $response = new JsonResponse();
        $response->headers->set('Access-Control-Allow-Origin', '*');

        $response->setStatusCode(200);
        $response->setContent(json_encode([
            'data' => [
                'countTotal' => $res['countTotal'],
                'cards' => $applications
            ]
        ]));

        return $response;
    }

    /**
     * @param Request $request
     * @param $id
     * @Route("/api/delete_application/{id}", methods={"POST", "OPTIONS"})
     * @return Response $response
     */
    public function removeAction(Request $request, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'no access');

        $data = json_decode($request->getContent(), true);

        $response = new JsonResponse();

        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Method', 'GET, PATCH, POST, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept');

        if ($request->isMethod('OPTIONS')) {

            $response->setStatusCode(200);
            $response->setContent(json_encode([
                'success' => true
            ]));

            return $response;
        }

        /**
         * @var Application $application
         */
        $application = $this->getDoctrine()
            ->getRepository(Application::class)->find($id);

        if (!$application) {
            $response->setStatusCode(404);
            $response->setContent(json_encode([
                'success' => false
            ]));

            return $response;
        }

        if ($application->getEaistoStatus() == 2) {
            $response->setStatusCode(403);
            $response->setContent(json_encode([
                'success' => false
            ]));

            return $response;
        }

        $em = $this->getDoctrine()->getManager();

        $em->remove($application);
        $em->flush();

        $response->setStatusCode(200);
        $response->setContent(json_encode([
            'success' => true,
            'deleted' => true,
            'id' => $id
        ]));

        return $response;
    }

    /**
     * @param Request $request
     * @param $id
     * @Route("/api/register_application/{id}", methods={"POST", "OPTIONS"})
     * @return Response $response
     */
    public function registerAction(Request $request, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'no access');

        $data = json_decode($request->getContent(), true);

        $response = new JsonResponse();

        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Method', 'GET, PATCH, POST, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept');

        if ($request->isMethod('OPTIONS')) {

            $response->setStatusCode(200);
            $response->setContent(json_encode([
                'success' => true
            ]));

            return $response;
        }

        /**
         * @var Application $application
         */
        $application = $this->getDoctrine()
            ->getRepository(Application::class)->find($id);

        $operators = $this->getDoctrine()->getRepository(Operator::class)->findBy([
            'isActive' => true
        ], [
            'id' => 'ASC'
        ]);

        /**
         * @var Application $latestApplication
         */
        $latestApplications = $this->getDoctrine()->getRepository(Application::class)->findBy(
           [
              'eaistoStatus' => 2
           ], [
               'id' => 'DESC'
            ]
        );

        $latestOperator = $operators[0];

        if ($latestApplications) {
            $latestApplication = $latestApplications[0];
            $latestOperator = $latestApplication->getOperator();
        }

        $operators = $this->getDoctrine()->getRepository(Operator::class)->findBy([
            'isActive' => true
        ], [
            'id' => 'ASC'
        ]);

        if (!$latestOperator) {
            $latestOperator = $operators[0];
        }

        $nextOperators = [];

        /**
         * @var Operator $operator
         */
        foreach ($operators as $operator) {
            if ($operator->getId() > $latestOperator->getId()) {
                $nextOperators[] = $operator;
            }
        }

        if (!$nextOperators) {
            /**
             * @var Operator $operator
             */
            foreach ($operators as $operator) {
                if ($operator->getId() < $latestOperator->getId()) {
                    $nextOperators[] = $operator;
                }
            }
        }

        if (!$nextOperators) {
            $nextOperators[] = $latestOperator;
        }//dump($nextOperators);die();

        $operatorCategories = $this->getDoctrine()->getRepository(OperatorCategory::class)->findAll();

        $allowedOperatorsIds = [];

        if ($application->getVehicleCategory()) {
            /**
             * @var OperatorCategory $operatorCategory
             */
            foreach ($operatorCategories as $operatorCategory) {
                if ($application->getVehicleCategory()->getId() == $operatorCategory->getCategory()->getId()) {
                    $allowedOperatorsIds[] = $operatorCategory->getOperator()->getId();
                }
            }
        } else {
            $response->setContent(json_encode([
                'success' => false,
                'message' => 'Не указана категория авто'
            ]));

            return $response;
        }

        $limits = $this->getDoctrine()->getRepository(Limit::class)->checkLimits();

        $chosenOperators = [$nextOperators[0]];

        foreach ($allowedOperatorsIds as $allowedOperatorsId) {
            /**
             * @var Operator $nextOperator
             */
            foreach ($nextOperators as $nextOperator) {
                foreach ($limits as $limit) {
                    if ($allowedOperatorsId == $nextOperator->getId()) {
                        if ($limit['operatorId'] == $nextOperator->getId()) {
                            if ($limit['available']) {
                                //$chosenOperators = [];
                                $chosenOperators[] = $nextOperator;
                            }
                        }
                    }
                }
            }
        }

        /**
         * @var Operator $chosenOperator
         */
        $chosenOperator = $chosenOperators[1];

        $application->setOperator($chosenOperator);

        if ($chosenOperator->getRegNumber() == '08870') {
            $application->setExpert(1);
        } elseif ($chosenOperator->getRegNumber() == '00429') {
            $currentDay = (new \DateTime())->format('d');

            if ($currentDay % 2 === 0) {
                $application->setExpert(2);
            } else {
                $application->setExpert(3);
            }
        } elseif ($chosenOperator->getRegNumber() == '08489') {
            $application->setExpert(4);
        } elseif ($chosenOperator->getRegNumber() == '09115') {
            $application->setExpert(5);
        }

        /**
         * @var SoapUserInterface $expert
         */
        $expert = $this->getDoctrine()->getRepository(Expert::class)->find($application->getExpert());

        $soapService = new Soap($expert, $this->container, $operator->getRegNumber());
        $eaistoInfo = $soapService->registerCard($application);

        if (!empty($eaistoInfo->Nomer)) {
            $application->setEaistoNumber($eaistoInfo->Nomer);
            $application->setEaistoId($eaistoInfo->RegisterCardResult);
            $application->setEaistoDate(time());
            $application->setEaistoStatus(2);
        } else {
            $response->setStatusCode(200);
            $response->setContent(json_encode([
                'success' => false,
//                'eaistoNumber' => 123456765456456,
//                'id' => $application->getId(),
                'message' => $eaistoInfo->getMessage()
            ]));

            return $response;
        }

        $em = $this->getDoctrine()->getManager();

        $em->persist($application);
        $em->flush();

        $response->setContent(json_encode([
            'success' => true,
            'id' => $application->getId(),
            'eaistoNumber' => $application->getEaistoNumber()
        ]));

//        $response->setContent(json_encode([
//            'limits' => $limits,
//            'operator' => [
//                'id' => $chosenOperators[1]->getId(),
//                'name' => $chosenOperators[1]->getFullName()
//            ],
//            'allowedOperatorsIds' => $allowedOperatorsIds,
//            'nextOperators' => array_map(function($operator) {
//                return [
//                    'id' => $operator->getId(),
//                    'name' => $operator->getFullName()
//                ];
//            }, $nextOperators),
//            'chosenOperators' => array_map(function($operator) {
//                return [
//                    'id' => $operator->getId(),
//                    'name' => $operator->getFullName()
//                ];
//            }, $chosenOperators)
//        ]));

        return $response;
    }

    /**
     * @param Request $request
     * @Route("/open_api/photo_applications", methods={"POST", "OPTIONS"})
     * @return Response $response
     */
    public function createWithPhotoAction(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $response = new JsonResponse();

        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Method', 'GET, PATCH, POST, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept');

        if ($request->isMethod('OPTIONS')) {

            $response->setStatusCode(200);
            $response->setContent(json_encode([
                'success' => true
            ]));

            return $response;
        }

        if ($errors = $this->validatePhotoCard($data)) {
            $response->setContent(json_encode([
                'data' => [
                    'success' => false,
                    'errors' => $errors
                ]
            ]));

            return $response;
        }

        $entity = new Application();
        $entity->setCreatedAt(time());

        $entity->setCardIsSecondary(false);
        $entity->setIsArchive(false);
        $entity->setPropertyType(1);
        $entity->setDateOfDiagnosis(time());
        $entity->setTestResult(true);
        $entity->setTestType(1);
        $entity->setTyres($data['tyres']);
        $entity->setKilometres($data['kilometres']);
        $entity->setStatus(2);
        $entity->setIssuedAt(time());
        $entity->setType(2);

        $entity->setEmail($data['email']);
        $entity->setPhone($data['phone'] ?? null);
        $entity->setEaistoStatus(1);
        $entity->setPurchased(false);

        $entity->setPhoto1($data['file1']);
        $entity->setPhoto2($data['file2']);

        $em = $this->getDoctrine()->getManager();

        $em->persist($entity);
        $em->flush();

        $response->setStatusCode(200);
        $response->setContent(json_encode([
            'data' => [
                'success' => true,
                'message' => '<strong>Спасибо!</strong> <span>Заявка успешно отправлена.</span> <span>Дагностическая карта будет отправлена на ваш e-mail через несколько минут!</span>'
            ]
        ]));

        return $response;
    }

    /**
     * @param Request $request
     * @Route("/open_api/photos", methods={"POST", "OPTIONS"})
     * @return Response $response
     */
    public function uploadImageAction(Request $request, LoggerInterface $logger)
    {
        $response = new JsonResponse();

        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Method', 'GET, PATCH, POST, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept');

        $logger->info('TRYING UPLOAD FILE', [
            'userIp' => $request->getClientIp()
        ]);

        if ($request->isMethod('OPTIONS')) {

            $response->setStatusCode(200);
            $response->setContent(json_encode([
                'success' => true
            ]));

            return $response;
        }

        /**
         * @var UploadedFile $file
         */
        $file = $request->files->get('file');

        $logger->info('UPLOADED FILE INFO', [
            'size' => $file->getSize(),
            'extension' => $file->guessExtension(),
            'clientExtension' => $file->guessClientExtension()
        ]);

        if ($file->getSize() > 20 * 1000 * 1000) {
            $response->setContent(json_encode([
                'success' => false,
                'message' => 'Превышен допустимый размер в 20МБ'
            ]));

            return $response;
        }

        if (!in_array($file->guessExtension(), ['jpg', 'jpeg', 'png', 'gif'])) {
            $response->setContent(json_encode([
                'success' => false,
                'message' => 'Недопустимое расширение'
            ]));

            return $response;
        }

        $fileName = md5(uniqid()) . md5(time()) . '.' . $file->guessExtension();

        $file->move(
            $this->getParameter('images_folder'),
            $fileName
        );

        $response->setContent(json_encode([
            'success' => true,
            'fileName' => $fileName
        ]));

        return $response;
    }

    /**
     * @param Request $request
     * @param $id
     * @param DCValidator $validator
     * @Route("/api/edit_application/{id}", methods={"POST", "OPTIONS"})
     * @return Response $response
     */
    public function editAction(Request $request, $id, DCValidator $validator)
    {
        $data = json_decode($request->getContent(), true);

        $response = new JsonResponse();

        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Method', 'GET, PATCH, POST, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept');

        if ($request->isMethod('OPTIONS')) {

            $response->setStatusCode(200);
            $response->setContent(json_encode([
                'success' => true
            ]));

            return $response;
        }

        if ($errors = $validator->validateCard($data)) {
            $response->setContent(json_encode([
                'data' => [
                    'success' => false,
                    'errors' => $errors
                ]
            ]));

            return $response;
        }

        /**
         * @var Application $entity
         */
        $entity = $this->getDoctrine()->getRepository(Application::class)->find($id);

        if (!$entity) {
            $response->setStatusCode(404);
            $response->setContent(json_encode([
                'data' => [
                    'success' => false,
                    'message' => 'No card existing'
                ]
            ]));

            return $response;
        }

        $entity->setUpdatedAt(time());

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

        $carMarkName = $data['carMarkName'];
        $carModelName = $data['carModelName'];

        $entity->setOwnerFirstName(trim($data['ownerFirstName']));
        $entity->setOwnerLastName(trim($data['ownerLastName']));
        $entity->setOwnerMiddleName(trim($data['ownerMiddleName'] ?? ''));
        $entity->setCardIsSecondary(false);
        $entity->setIsArchive(false);
        $entity->setBodyNumber($data['bodyNumber'] ?? null);
        $entity->setDateOfDiagnosis(time());
        $entity->setValidTill($validTill);
        $entity->setTestResult(true);
        $entity->setTestType(1);
        $entity->setVehicleCategory($vehicleCategory);
        $entity->setVin($data['vin'] ?? null);
        $entity->setEmptyMass($data['emptyMass']);
        $entity->setMaxMass($data['maxMass']);
        $entity->setFuelType($data['fuelType']);
        $entity->setBrakingSystem($data['brakingSystem']);
        $entity->setTyres($data['tyres']);
        $entity->setKilometres($data['kilometres']);
        $entity->setCarMarkName($carMarkName);
        $entity->setCarModelName($carModelName);
        $entity->setCarYear($data['carYear']);
        $entity->setDocumentType($data['documentType']);
        $entity->setDocumentSeries($data['documentSeries']);
        $entity->setDocumentNumber($data['documentNumber']);
        $entity->setDocumentOrganization($data['documentOrganization']);
        $entity->setDocumentDate($data['documentDate']);
        $entity->setFrameNumber($data['frameNumber'] ?? null);
        $entity->setRegistrationNumber($data['registrationNumber']);
        $entity->setLegalName($data['legalName'] ?? null);
        $entity->setPropertyType($data['propertyType']);
        $entity->setStatus(2);
        $entity->setIssuedAt(time());
        //$entity->setNote($note);
        //$entity->setType(1);

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
     * @Route("/open_api/applications", methods={"POST", "OPTIONS"})
     * @return Response $response
     */
    public function createAction(Request $request)
    {

        $data = json_decode($request->getContent(), true);

        $response = new JsonResponse();

        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Method', 'GET, PATCH, POST, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept');

        if ($request->isMethod('OPTIONS')) {

            $response->setStatusCode(200);
            $response->setContent(json_encode([
                'success' => true
            ]));

            return $response;
        }

        if ($errors = $this->validateCard($data)) {
            $response->setContent(json_encode([
                'data' => [
                    'success' => false,
                    'errors' => $errors
                ]
            ]));

            return $response;
        }

        $entity = new Application();
        $entity->setCreatedAt(time());

        $validTill = (new \DateTime())->modify('+24 months')->getTimestamp();

        if (date('Y') - $data['year'] >= 7) {
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

        $carMarkName = $data['carMarkName'];
        $carModelName = $data['carModelName'];

        $entity->setOwnerFirstName(trim($data['ownerFirstName']));
        $entity->setOwnerLastName(trim($data['ownerLastName']));
        $entity->setOwnerMiddleName(trim($data['ownerMiddleName'] ?? '') ??  'ОТСУТСТВУЕТ');
        $entity->setCardIsSecondary(false);
        $entity->setIsArchive(false);
        $entity->setBodyNumber($data['bodyNumber'] ?? 'ОТСУТСТВУЕТ');
        $entity->setDateOfDiagnosis(time());
        $entity->setValidTill($validTill);
        $entity->setTestResult(true);
        $entity->setTestType(1);
        $entity->setVehicleCategory($vehicleCategory);
        $entity->setVin($data['vin'] ?? 'ОТСУТСТВУЕТ');
        $entity->setEmptyMass($data['emptyMass']);
        $entity->setMaxMass($data['maxMass']);
        $entity->setFuelType($data['fuelType']);
        $entity->setBrakingSystem($data['brakeSystem']);
        $entity->setTyres($data['tyres']);
        $entity->setKilometres($data['kilometres']);
        $entity->setCarMarkName($carMarkName);
        $entity->setCarModelName($carModelName);
        $entity->setCarYear($data['year']);
        $entity->setDocumentType($data['documentType']);
        $entity->setDocumentSeries($data['documentSeries']);
        $entity->setDocumentNumber($data['documentNumber']);
        $entity->setDocumentOrganization($data['documentOrganization']);
        $entity->setDocumentDate($data['documentDate']);
        $entity->setFrameNumber($data['frameNumber'] ?? 'ОТСУТСТВУЕТ');
        $entity->setRegistrationNumber($data['registrationNumber'] ?? 'ОТСУТСТВУЕТ');
        $entity->setLegalName($data['legalName'] ?? null);
        $entity->setPropertyType($data['propertyType']);
        $entity->setStatus(2);
        $entity->setIssuedAt(time());
        $entity->setNote($data['note']);
        $entity->setType(1);

        $entity->setEmail($data['email']);
        $entity->setPhone($data['phone'] ?? null);
        $entity->setEaistoStatus(1);
        $entity->setPurchased(false);
        $entity->setCity($data['city']);

        $em = $this->getDoctrine()->getManager();

        $em->persist($entity);
        $em->flush();

        $response->setStatusCode(200);
        $response->setContent(json_encode([
            'data' => [
                'success' => true,
                'message' => '<strong>Спасибо!</strong> <span>Заявка успешно отправлена.</span> <span>Дагностическая карта будет отправлена на ваш e-mail через несколько минут!</span>'
            ]
        ]));

        return $response;
    }

    private function validatePhotoCard($data)
    {
        $errors = [];

        if (Validator::notExistsOrEmpty('email', $data)) {
            $errors['email'][] = 'Email должен быть указан';
        }

        if (Validator::notExistsOrEmpty('phone', $data)) {
            $errors['phone'][] = 'Телефон должен быть указан';
        }

        if (Validator::notExistsOrEmpty('kilometres', $data)) {
            $errors['kilometres'][] = 'Пробег должен быть указан';
        }

        if (Validator::notExistsOrEmpty('tyres', $data)) {
            $errors['tyres'][] = 'Марка шин должна быть указана';
        }

        if (Validator::notExistsOrEmpty('file1', $data)) {
            $errors['file1'][] = 'Не загружено фото';
        }

        if (Validator::notExistsOrEmpty('file2', $data)) {
            $errors['file2'][] = 'Не загружено фото';
        }

        return $errors;
    }

    private function validateCard($data)
    {
        $errors = [];

        if (!array_key_exists('propertyType', $data)) {
            $errors['propertyType'][] = 'Поле должно быть указано';
        }

        if (!in_array($data['propertyType'], [1, 2])) {
            $errors['propertyType'][] = 'Некорректный тип собственности';
        }

        if (Validator::notExistsOrEmpty('ownerFirstName', $data)) {
            $errors['ownerFirstName'][] = 'Имя должно быть указано';
        }

        if (!Validator::existsAndInRange('ownerFirstName', $data, 2, 100)) {
            $errors['ownerFirstName'][] = 'Длина должна быть от 2 до 100 символов';
        }

        if (!Validator::existsAndInRange('ownerLastName', $data, 2, 100)) {
            $errors['ownerLastName'][] = 'Длина должна быть от 2 до 100 символов';
        }

        if (Validator::notExistsOrEmpty('ownerLastName', $data)) {
            $errors['ownerLastName'][] = 'Фамилия должна быть указана';
        }

        if (!Validator::notExistsOrEmpty('ownerMiddleName', $data) && !Validator::existsAndInRange('ownerMiddleName', $data, 2, 100)) {
            $errors['ownerMiddleName'][] = 'Длина должна быть от 2 до 100 символов';
        }

        if (!Validator::notExistsOrEmpty('legalName', $data) && !Validator::existsAndInRange('legalName', $data, 2, 200)) {
            $errors['legalName'][] = 'Длина должна быть от 1 до 200 символов';
        }

        if (Validator::notExistsOrEmpty('email', $data)) {
            $errors['email'][] = 'Email должен быть указан';
        }

        if (Validator::notExistsOrEmpty('phone', $data)) {
            $errors['phone'][] = 'Телефон должен быть указан';
        }

        if (Validator::notExistsOrEmpty('city', $data)) {
            $errors['city'][] = 'Город должен быть указан';
        }

        if (Validator::notExistsOrEmpty('carMarkName', $data)) {
            $errors['carMarkName'][] = 'Марка должна быть указана';
        }

        if (Validator::notExistsOrEmpty('carModelName', $data)) {
            $errors['carModelName'][] = 'Модель должна быть указана';
        }

        if (Validator::notExistsOrEmpty('year', $data)) {
            $errors['year'][] = 'Год должен быть указан';
        }

        if (Validator::notExistsOrEmpty('kilometres', $data)) {
            $errors['kilometres'][] = 'Пробег должен быть указан';
        }

        if (Validator::notExistsOrEmpty('maxMass', $data)) {
            $errors['maxMass'][] = 'Макс. масса должна быть указана';
        }

        if (Validator::notExistsOrEmpty('emptyMass', $data)) {
            $errors['emptyMass'][] = 'Масса без нагрузки должна быть указана';
        }

        if (Validator::notExistsOrEmpty('vehicleCategory', $data)) {
            $errors['vehicleCategory'][] = 'Категория должна быть указана';
        }

        if (Validator::notExistsOrEmpty('registrationNumber', $data)) {
            $errors['registrationNumber'][] = 'Рег. знак должен быть указан';
        }

        if (Validator::notExistsOrEmpty('tyres', $data)) {
            $errors['tyres'][] = 'Марка шин должна быть указана';
        }

        if (Validator::notExistsOrEmpty('fuelType', $data)) {
            $errors['fuelType'][] = 'Тип топлива должен быть указан';
        }

        if (Validator::notExistsOrEmpty('documentType', $data)) {
            $errors['documentType'][] = 'Тип документа должен быть указан';
        }

        if (Validator::notExistsOrEmpty('documentNumber', $data)) {
            $errors['documentNumber'][] = 'Номер документа должен быть указан';
        }

        if (Validator::notExistsOrEmpty('documentSeries', $data)) {
            $errors['documentSeries'][] = 'Серия документа должна быть указана';
        }

        if (Validator::notExistsOrEmpty('documentOrganization', $data)) {
            $errors['documentOrganization'][] = 'Организация должна быть указана';
        }

        if (Validator::notExistsOrEmpty('documentDate', $data)) {
            $errors['documentDate'][] = 'Дата должна быть указана';
        }

        if (array_key_exists('vin', $data) && !empty($data['vin']) && !preg_match('#^[0123456789ABCDEFGHJKLMNPRSTUVWXYZ]{17}$#', $data['vin'])) {
            $errors['vin'][] = 'Некорректный VIN';
        }

        if (!$this->validateIdentity($data)) {
            $errors['vin'][] = $errors['bodyNumber'][] = $errors['frameNumber'][] = 'Хотя бы один идентифекационный параметр (vin, номер рамы, номер кузова) должен быть указан';
        }

        if (!array_key_exists('brakeSystem', $data)) {
            $errors['brakeSystem'][] = 'Тормозная система должна быть указана';
        }

        if (!Validator::notEmpty('brakeSystem', $data)) {
            $errors['brakeSystem'][] = 'Тормозная система должна быть указана';
        }

        if (!Validator::notExistsOrEmpty('maxMass', $data) && !Validator::notExistsOrEmpty('emptyMass', $data)) {
            if ($data['maxMass'] < $data['emptyMass']) {
                $errors['maxMass'][] = $errors['emptyMass'][] = 'Масса без нагрузки не может быть больше максимальной массы';
            }
        }

        if (!Validator::notExistsOrEmpty('documentDate', $data) && !Validator::notExistsOrEmpty('year', $data)) {
            if ((new \DateTime())->setTimestamp($data['documentDate'])->format('Y') < $data['year']) {
                $errors['year'][] = $errors['documentDate'][] = 'Дата выдачи документа не может быть меньше даты выпуска автомобиля';
            }
        }

        if (Validator::notEmpty('frameNumber', $data) && mb_strlen($data['frameNumber']) < 6) {
            $errors['frameNumber'][] = 'Номер рамы должен состоять хотя бы из 6 символов';
        }

        if (Validator::notEmpty('bodyNumber', $data) && mb_strlen($data['bodyNumber']) < 5) {
            $errors['bodyNumber'][] = 'Номер кузова должен состоять хотя бы из 5 символов';
        }

        return $errors;

    }

    private function validateIdentity($data)
    {
        $vinExists = array_key_exists('vin', $data) && !empty($data['vin']);
        $bodyNumberExists = array_key_exists('bodyNumber', $data) && !empty($data['bodyNumber']);
        $frameNumberExists = array_key_exists('frameNumber', $data) && !empty($data['frameNumber']);

        return $vinExists || $bodyNumberExists || $frameNumberExists;
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