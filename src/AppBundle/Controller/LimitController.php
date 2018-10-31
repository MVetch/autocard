<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 04.02.18
 * Time: 10:08
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Category;
use AppBundle\Entity\CategoryLimit;
use AppBundle\Entity\Limit;
use AppBundle\Entity\Operator;
use AppBundle\Entity\OperatorLimit;
use AppBundle\Entity\User;
use AppBundle\Entity\UserLimit;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LimitController extends Controller
{

    /**
     * @return JsonResponse
     * @Route("/api/limits")
     * @Method("GET")
     */
    public function limitsAction()
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'no access');

        /**
         * @var User $currentUser
         */
        $currentUser = $this->get('security.token_storage')->getToken()->getUser();

        $limits = $this->getDoctrine()->getRepository(Limit::class)->findByUserId($currentUser->getId());

        $response = new JsonResponse();
        $response->headers->set('Access-Control-Allow-Origin', '*');

        $response->setContent(json_encode([
            'success' => true,
            'data' => [
                'limits' => $limits
            ]
        ]));

        return $response;
    }

    /**
     * @param Request $request
     * @param $id
     * @return JsonResponse
     * @Route("/api/limits/{id}")
     */
    public function getLimitAction(Request $request, $id)
    {

        $this->denyAccessUnlessGranted('ROLE_USER', null, 'no access');

        $response = new JsonResponse();

        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Method', 'GET, PUT, POST, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type');

        /**
         * @var Limit $limit
         */
        $limit = $this->getDoctrine()->getRepository(Limit::class)->find($id);

        $categoryGroups = $this->getDoctrine()->getRepository(Limit::class)->findGroupCategories($limit->getId());
        $users = $this->getDoctrine()->getRepository(Limit::class)->findUsers($limit->getId());
        $operators = $this->getDoctrine()->getRepository(Limit::class)->findOperators($limit->getId());

        $response->setStatusCode(200);
        $response->setContent(json_encode([
            'success' => true,
            'data' => [
                'limit' => [
                    'name' => $limit->getName(),
                    'workingHours' => $limit->getWorkingHours(),
                    'limitation' => $limit->getLimitation(),
                    'categoryGroups' => $categoryGroups,
                    'users' => $users,
                    'operators' => $operators
                ]
            ]
        ]));

        return $response;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/api/deleteLimit", methods={"POST", "OPTIONS"})
     */
    public function deleteLimitAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'no access');

        $data = json_decode($request->getContent(), true);

        $response = new JsonResponse();

        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Method', 'GET, PUT, POST, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type');

        if ($request->isMethod('OPTIONS')) {
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
         * @var Limit $limit
         */
        $limit = $this->getDoctrine()->getRepository(Limit::class)->findOneBy(['id' => $data['id']]);

        if ($currentUser->getId() != $limit->getUser()->getId()) {
            $response->setContent(json_encode([
                'success' => false
            ]));

            return $response;
        }

        $em = $this->getDoctrine()->getManager();

        /**
         * @var CategoryLimit[] $categoryLimits
         */
        $categoryLimits = $this->getDoctrine()->getRepository(CategoryLimit::class)->findBy(['limit' => $limit]);

        foreach ($categoryLimits as $limitRelation) {
            $em->remove($limitRelation);
        }

        /**
         * @var OperatorLimit[] $operatorLimits
         */
        $operatorLimits = $this->getDoctrine()->getRepository(OperatorLimit::class)->findBy(['limit' => $limit]);

        foreach ($operatorLimits as $limitRelation) {
            $em->remove($limitRelation);
        }

        /**
         * @var UserLimit[] $userLimits
         */
        $userLimits = $this->getDoctrine()->getRepository(UserLimit::class)->findBy(['limit' => $data['id']]);

        foreach ($userLimits as $limitRelation) {
            $em->remove($limitRelation);
        }

        $em->remove($limit);
        $em->flush();

        $response->setContent(json_encode([
            'success' => true
        ]));

        return $response;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/api/limits", methods={"POST", "OPTIONS"})
     */
    public function createLimitAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER', null, 'no access');

        $data = json_decode($request->getContent(), true);

        $response = new JsonResponse();

        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Method', 'GET, PUT, POST, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type');

        if ($request->isMethod('OPTIONS')) {
            $response->setContent(json_encode([
                'success' => true
            ]));

            return $response;
        }

        $currentUser = $this->get('security.token_storage')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();

        if (!empty($data['id'])) {
            $limit = $this->getDoctrine()->getRepository(Limit::class)->find($data['id']);
        } else {
            $limit = new Limit();
        }

        $limit->setName($data['name']);
        $limit->setUser($currentUser);
        $limit->setLimitation($data['limit']);
        $limit->setWorkingHours($data['workingHours']);

        if (!empty($data['id'])) {

            /**
             * @var CategoryLimit[] $categoryLimits
             */
            $categoryLimits = $this->getDoctrine()->getRepository(CategoryLimit::class)->findBy(['limit' => $limit]);

            foreach ($categoryLimits as $limitRelation) {
                $em->remove($limitRelation);
            }

            /**
             * @var OperatorLimit[] $operatorLimits
             */
            $operatorLimits = $this->getDoctrine()->getRepository(OperatorLimit::class)->findBy(['limit' => $limit]);

            foreach ($operatorLimits as $limitRelation) {
                $em->remove($limitRelation);
            }

            /**
             * @var UserLimit[] $userLimits
             */
            $userLimits = $this->getDoctrine()->getRepository(UserLimit::class)->findBy(['limit' => $data['id']]);

            foreach ($userLimits as $limitRelation) {
                $em->remove($limitRelation);
            }

        } else {
            $limit->setIsActive(true);
        }

        $em->persist($limit);

        foreach ($data['categoryGroups'] as $group) {
            $categories = $this->getDoctrine()->getRepository(Category::class)->findBy(['letter' => $group['id']]);

            foreach ($categories as $category) {
                $categoryLimit = new CategoryLimit();

                $categoryLimit->setCategory($category);
                $categoryLimit->setLimit($limit);

                $em->persist($categoryLimit);
            }
        }

        foreach ($data['users'] as $user) {
            $user = $this->getDoctrine()->getRepository(User::class)->find($user['id']);

            $userLimit = new UserLimit();

            $userLimit->setUser($user);
            $userLimit->setLimit($limit);

            $em->persist($userLimit);
        }

        foreach ($data['operators'] as $operator) {
            $operator = $this->getDoctrine()->getRepository(Operator::class)->find($operator['id']);

            $operatorLimit = new OperatorLimit();

            $operatorLimit->setOperator($operator);
            $operatorLimit->setLimit($limit);

            $em->persist($operatorLimit);
        }

        $em->flush();

        $response->setContent(json_encode([
            'success' => true
        ]));

        return $response;
    }

    /**
     * @param Request $request
     * @Route("/api/limitStatus", methods={"POST", "OPTIONS"})
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
         * @var Limit $limit
         */
        $limit = $this->getDoctrine()->getRepository(Limit::class)->findOneBy(['id' => $data['id']]);

        if ($currentUser->getId() != $limit->getUser()->getId()) {
            $response->setContent(json_encode([
                'success' => false
            ]));

            return $response;
        }

        $limit->setIsActive($data['isActive']);

        $em = $this->getDoctrine()->getManager();

        $em->persist($limit);
        $em->flush();

        $response->setStatusCode(200);

        $response->setContent(json_encode([
            'success' => true
        ]));

        return $response;
    }

}