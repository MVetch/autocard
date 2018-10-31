<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 29.01.18
 * Time: 11:36
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Cities;
use AppBundle\Entity\User;
use AppBundle\Security\ApiTokenEncoder;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends Controller
{

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/api/agents")
     */
    public function agentsAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'no access');

        $excludeCurrent = $request->query->get('excludeCurrent') ?? null;

        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('security.token_storage')->getToken()->getUser();

        $users = $this->getDoctrine()->getRepository(User::class)->findAllByUserId($currentUser->getId(), (int) $excludeCurrent);

        $response = new JsonResponse();
        $response->headers->set('Access-Control-Allow-Origin', '*');

        $response->setContent(json_encode([
            'success' => true,
            'data' => [
                'agents' => $users
            ]
        ]));

        return $response;
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     * @Route("/api/agents/{id}")
     */
    public function agentAction(Request $request, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'no access');

        /**
         * @var User $agent
         */
        $agent = $this->getDoctrine()->getRepository(User::class)->find($id);

        $data['id'] = $agent->getId();
        $data['name'] = $agent->getName();
        $data['login'] = $agent->getLogin();
        $data['email'] = $agent->getEmail();
        $data['ips'] = $agent->getIps() ? $agent->getIps() : [];
        $data['priceA'] = $agent->getPriceA();
        $data['priceB'] = $agent->getPriceB();
        $data['priceC'] = $agent->getPriceC();
        $data['priceD'] = $agent->getPriceD();
        $data['priceE'] = $agent->getPriceE();

        $data['city'] = [
            'id' => $agent->getCity()->getId(),
            'name' => $agent->getCity()->getName()
        ];

        $response = new JsonResponse();
        $response->headers->set('Access-Control-Allow-Origin', '*');

        $response->setContent(json_encode([
            'success' => true,
            'data' => [
                'agent' => $data
            ]
        ]));

        return $response;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/api/cities")
     */
    public function citiesAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'no access');

        $q = $request->query->get('q') ?? null;

        $cities = $this->getDoctrine()->getRepository(User::class)->findCitiesByTerm($q);

        $response = new JsonResponse();
        $response->headers->set('Access-Control-Allow-Origin', '*');

        $response->setContent(json_encode([
            'success' => true,
            'data' => [
                'cities' => $cities
            ]
        ]));

        return $response;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/api/current_user", methods={"GET", "OPTIONS"})
     */
    public function currentUserAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'no access');

        $response = new JsonResponse();

        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Method', 'GET, PUT, POST, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type');

        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('security.token_storage')->getToken()->getUser();

        $response->setContent(json_encode([
            'success' => true,
            'result' => [
                'login' => $currentUser->getLogin(),
                'name' => $currentUser->getName()
            ]
        ]));

        return $response;
    }

    /**
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param EncoderFactoryInterface $factory
     * @return JsonResponse
     * @Route("/api/changePassword", methods={"POST", "OPTIONS"})
     */
    public function changePasswordAction(Request $request, UserPasswordEncoderInterface $passwordEncoder, EncoderFactoryInterface $factory)
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'no access');

        $data = json_decode($request->getContent(), true);

        $response = new JsonResponse();

        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Method', 'GET, PUT, POST, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type');

        if ($errors = $this->validatePasswordChange($data)) {
            $response->setContent(json_encode([
                'success' => false,
                'data' => [
                    'errors' => $errors
                ]
            ]));

            return $response;
        }

        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('security.token_storage')->getToken()->getUser();

        $encoder = $factory->getEncoder($currentUser);

        if (!$encoder->isPasswordValid($currentUser->getPassword(), $data['passwordCurrent'], $currentUser->getSalt())) {
            $response->setStatusCode(200);
            $response->setContent(json_encode([
                'success' => false,
                'data' => [
                    'errors' => [
                        'passwordCurrent' => [
                            'Неверный пароль'
                        ]
                    ]
                ]
            ]));

            return $response;
        }

        $passwordNew = $passwordEncoder->encodePassword($currentUser, $data['passwordNew']);

        $currentUser->setPassword($passwordNew);

        $em = $this->getDoctrine()->getManager();

        $em->persist($currentUser);
        $em->flush();

        $response->setContent(json_encode([
            'success' => true
        ]));

        return $response;
    }

    /**
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param ApiTokenEncoder $tokenEncoder
     * @Route("/api/users", methods={"POST", "OPTIONS"})
     * @return Response $response
     */
    public function createAgentAction(Request $request, UserPasswordEncoderInterface $passwordEncoder, ApiTokenEncoder $tokenEncoder)
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'no access');

        $data = json_decode($request->getContent(), true);

        $response = new JsonResponse();

        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Method', 'GET, PUT, POST, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type');

        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('security.token_storage')->getToken()->getUser();

        if (!$currentUser->getisActive()) {
            $response->setContent(json_encode([
                'data' => [
                    'success' => false,
                    'errors' => [
                        'alert' => [
                            'messages' => [
                                'Ваша учетная запись заблокирована!'
                            ]
                        ]
                    ],
                ]
            ]));

            return $response;
        }

        if (!empty($data['id'])) {
            if ($errors = $this->validateUser($data, false)) {
                $response->setContent(json_encode([
                    'success' => false,
                    'data' => [
                        'errors' => $errors
                    ]
                ]));

                return $response;
            }

            $user = $this->getDoctrine()->getRepository(User::class)->find($data['id']);

        } else {
            if ($errors = $this->validateUser($data, true)) {
                $response->setContent(json_encode([
                    'success' => false,
                    'data' => [
                        'errors' => $errors
                    ]
                ]));

                return $response;
            }

            $user = new User();
        }

        $city = $this->getDoctrine()->getRepository(Cities::class)->find($data['city']);

        $user->setName($data['name']);

        if ($data['email'] != '') {
            $user->setEmail($data['email']);
        }

        $user->setCity($city);
        $user->setPriceA($data['priceA']);
        $user->setPriceB($data['priceB']);
        $user->setPriceC($data['priceC']);
        $user->setPriceD($data['priceD']);
        $user->setPriceE($data['priceE']);
        $user->setIps($data['ips']);
        $user->setParentId($currentUser->getId());

        if (empty($data['id'])) {
            $apiToken = $tokenEncoder->encode($data['login']);
            $password = $passwordEncoder->encodePassword($user, $data['password']);

            $user->setLogin($data['login']);
            $user->setPassword($password);
            $user->setIsActive(true);
            $user->setCreatedAt(time());
            $user->setApiToken($apiToken);
            $user->setRoles(['ROLE_USER']);
        }

        $em = $this->getDoctrine()->getManager();

        $em->persist($user);
        $em->flush();

        $response->setContent(json_encode([
            'success' => true
        ]));

        $response->setStatusCode(200);

        return $response;
    }

    /**
     * @param Request $request
     * @Route("/api/userStatus", methods={"POST", "OPTIONS"})
     * @return Response $response
     */
    public function handleStatusAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'no access');

        $data = json_decode($request->getContent(), true);

        $response = new JsonResponse();

        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Method', 'GET, PUT, POST, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type');

        if ($request->getMethod() == 'OPTIONS') {
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

        /**
         * @var User $changedUser
         */
        $changedUser = $this->getDoctrine()->getRepository(User::class)->findOneBy(['id' => $data['id']]);

        if ($currentUser->getId() != $changedUser->getParentId()) {
            $response->setContent(json_encode([
                'success' => false
            ]));

            return $response;
        }

        $changedUser->setIsActive($data['isActive']);

        $em = $this->getDoctrine()->getManager();

        $em->persist($changedUser);
        $em->flush();

        $response->setStatusCode(200);

        $response->setContent(json_encode([
            'success' => true
        ]));

        return $response;
    }

    /**
     * @param Request $request
     * @Route("/authentication/login", methods={"POST", "OPTIONS"})
     * @return Response $response
     */
    public function loginAction(Request $request, EncoderFactoryInterface $factory, ApiTokenEncoder $tokenEncoder)
    {
        $data = json_decode($request->getContent(), true);

        $login = $data['login'] ?? null;
        $plainPassword = $data['password'] ?? null;

        $response = new JsonResponse();

        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Method', 'GET, PUT, POST, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type');

        if ($login === null || $plainPassword === null) {
            $response->setStatusCode(200);
            $response->setContent(json_encode([
                'success' => false,
                'errors' => [
                    [
                        'Пользователь не зарегистрирован'
                    ]
                ]
            ]));

            return $response;
        }

        /**
         * @var User $user
         */
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['login' => $login]);

        if (!$user) {
            $response->setStatusCode(200);
            $response->setContent(json_encode([
                'success' => false,
                'errors' => [
                    [
                        'Пользователь не зарегистрирован'
                    ]
                ]
            ]));

            return $response;
        }

        if (!$user->getisActive()) {
            $response->setStatusCode(200);
            $response->setContent(json_encode([
                'success' => false,
                'errors' => [
                    [
                        'Пользователь заблокирован'
                    ]
                ]
            ]));

            return $response;
        }

        $passwordEncoder = $factory->getEncoder($user);

        if (!$passwordEncoder->isPasswordValid($user->getPassword(), $plainPassword, $user->getSalt())) {
            $response->setStatusCode(200);
            $response->setContent(json_encode([
                'success' => false,
                'errors' => [
                    [
                        'Неверные данные'
                    ]
                ]
            ]));

            return $response;
        }

        //$apiToken = $tokenEncoder->encode($login);

        $em = $this->getDoctrine()->getManager();

        //$user->setApiToken($apiToken);

        $em->persist($user);
        $em->flush();

        $response->setStatusCode(200);
        $response->setContent(json_encode([
            'success' => true,
            'data' => [
                'apiToken' => $user->getApiToken(),
                'login' => $user->getLogin()
            ]
        ]));

        return $response;
    }

    private function validatePasswordChange($data)
    {
        $errors = [];

        if (empty($data['passwordCurrent'])) {
            $errors['passwordCurrent'][] = 'Не указан текущий пароль';
        }

        if (empty($data['passwordNew'])) {
            $errors['passwordNew'][] = 'Новый пароль не может быть пустым';
        }

        return $errors;
    }

    private function validateUser($data, $isNew = false)
    {
        $errors = [];

        if (empty($data['login']) && $isNew) {
            $errors['login'][] = 'Логин должен быть указан';
        }

        if (empty($data['password']) && $isNew) {
            $errors['password'][] = 'Пароль должен быть указан';
        }

        if (empty($data['name'])) {
            $errors['name'][] = 'Имя должно быть указано';
        }

        if (empty($data['city'])) {
            $errors['city'][] = 'Город должен быть указан';
        }

        if (empty($data['priceA'])) {
            $errors['priceA'][] = 'Цена категории A должна быть указана';
        }

        if (empty($data['priceB'])) {
            $errors['priceB'][] = 'Цена категории B должна быть указана';
        }

        if (empty($data['priceC'])) {
            $errors['priceC'][] = 'Цена категории C должна быть указана';
        }

        if (empty($data['priceD'])) {
            $errors['priceD'][] = 'Цена категории D должна быть указана';
        }

        if (empty($data['priceE'])) {
            $errors['priceE'][] = 'Цена категории E должна быть указана';
        }

        if (!empty($data['ips']) && is_array($data['ips'])) {
            foreach ($data['ips'] as $ip) {
                if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                    $errors['ips'][] = 'Один или несколько IP указаны некорректно';

                    break;
                }
            }
        }

        if (!empty($data['login'])) {
            if (null != $this->getDoctrine()->getRepository(User::class)->findOneBy(['login' => $data['login']]) && $isNew) {
                $errors['login'][] = 'Логин занят';
            }
        }

        if (!empty($data['email'])) {
            if (null != $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $data['email']]) && $isNew) {
                $errors['email'][] = 'email занят';
            }

        }

        return $errors;
    }

}