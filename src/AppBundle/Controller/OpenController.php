<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 25.09.2018
 * Time: 14:34
 */

namespace AppBundle\Controller;


use AppBundle\Entity\BrakeType;
use AppBundle\Entity\CarMark;
use AppBundle\Entity\Category;
use AppBundle\Entity\FuelType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class OpenController extends Controller
{

    /**
     * @Route("/open_api/categories", name="meta_open_categories")
     */
    public function categoriesAction()
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        $response = new JsonResponse();
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Method', 'GET, PUT, POST, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type');

        $response->setStatusCode(200);
        $response->setContent(json_encode([
            'data' => [
                'categories' => $categories
            ]
        ]));

        return $response;
    }

    /**
     * @Route("/open_api/fuel", name="meta_open_fuel_types")
     */
    public function fuelTypesAction()
    {
        //$this->denyAccessUnlessGranted('ROLE_USER', null, 'no access');

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
     * @Route("/open_api/brakes", name="meta_open_brake_types")
     */
    public function brakeTypesAction()
    {
        //$this->denyAccessUnlessGranted('ROLE_USER', null, 'no access');

        $result = $this->getDoctrine()->getRepository(BrakeType::class)->findAll();

        $response = new JsonResponse();
        $response->headers->set('Access-Control-Allow-Origin', '*');

        $response->setContent(json_encode([
            'data' => [
                'brakeTypes' => $result
            ]
        ]));

        return $response;
    }

    /**
     * @param Request $request
     * @Route("/open_api/carMarks", name="meta_open_car_marks")
     */
    public function carMarksAction(Request $request)
    {
        //$this->denyAccessUnlessGranted('ROLE_USER', null, 'no access');

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

        $carMarks = $this->getDoctrine()->getRepository(CarMark::class)->findAllMarks($request->query->get('q') ?? null);

        $response->setContent(json_encode([
            'data' => [
                'carMarks' => $carMarks
            ]
        ]));

        return $response;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/open_api/carModels", name="meta_open_car_models")
     */
    public function carModelsAction(Request $request)
    {
        //$this->denyAccessUnlessGranted('ROLE_USER', null, 'no access');

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

        $carModels = $this->getDoctrine()->getRepository(CarMark::class)->findAllModelsByMarkId($request->query->get('markId') ?? null);

        $response->setContent(json_encode([
            'data' => [
                'carModels' => $carModels
            ]
        ]));

        return $response;
    }

    /**
     * @Route("/open_api/tyres", name="open_tyres")
     */
    public function tyresAction(Request $request)
    {
        //$this->denyAccessUnlessGranted('ROLE_USER', null, 'no access');

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

        $response->setContent(json_encode([
            'data' => [
                'tyres' => $this->getTyres()
            ]
        ]));

        return $response;
    }

    private function getTyres()
    {
        return [
            "Accelera",
            "Achilles",
            "Aeolus",
            "America",
            "Amtel",
            "Amtel-Vredestein",
            "Apollo",
            "Atturo",
            "Aurora",
            "Austone",
            "Austyre",
            "Avon",
            "AvsTyre",
            "BFGoodrich",
            "Barum",
            "Bridgestone",
            "CEAT",
            "Chengshan",
            "Continental",
            "Contyre",
            "Cooper",
            "Cordiant",
            "Daewoo",
            "Dayton",
            "Dean",
            "Debica",
            "Deestone",
            "Diplomat",
            "Dunlop",
            "Effiplus",
            "Eurotec",
            "Euzkadi",
            "Falken",
            "Fate",
            "Federal",
            "Fenix",
            "Firenza",
            "Firestone",
            "Firststop",
            "Flamingo",
            "Formula",
            "Fortuna",
            "Forward",
            "Fulda",
            "FullWay",
            "Fuzion",
            "GT",
            "General",
            "Gislaved",
            "Goodride",
            "Goodyear",
            "HIFLY",
            "Hankook",
            "Hercules",
            "Infinity",
            "Insa-Turbo",
            "Interstate",
            "Ironman",
            "Kelly",
            "Kelly Tires",
            "Kenda",
            "Kenex",
            "Kingstar",
            "Kirov",
            "Kleber",
            "Kormoran",
            "Kumho",
            "Lassa",
            "LingLong",
            "MRF",
            "Mabor",
            "Maloya",
            "Marangoni",
            "Marshal",
            "Mastercraft",
            "Matador",
            "Matador-Омскшина",
            "Maxtrek",
            "Maxxis",
            "Medved",
            "Metzeler",
            "Michelin",
            "Mickey Thompson",
            "Milestone",
            "Millennium",
            "Minerva",
            "Mitas",
            "Motomaster",
            "Nankang",
            "Neuton",
            "Nexen",
            "Nexen Roadian",
            "Nexen Roadstone",
            "Nitto",
            "Nokian",
            "Nordman",
            "Novex",
            "OHTSU",
            "Ornet",
            "PREMIORRI",
            "Pirelli",
            "Pneumant",
            "PointS",
            "President",
            "Rapid",
            "Regal",
            "Remington",
            "Riken",
            "Rockstone",
            "Rosava",
            "Rotex",
            "SIBUR",
            "Sagitar",
            "Sailun",
            "Satoya",
            "Sava",
            "Semperit",
            "Shinko",
            "Sportiva",
            "StarFire",
            "Starperformer",
            "Stunner",
            "Sumitomo",
            "Sumo",
            "Sunny",
            "Tigar",
            "Toyo",
            "Tracmax",
            "Trayal",
            "Tri Ace",
            "Triangle",
            "Tunga",
            "Tyrex",
            "Uniroyal",
            "VSP",
            "Valsa",
            "Viatti",
            "Viking",
            "Voltyre",
            "Vredestein",
            "Wanli",
            "Westlake",
            "Yokohama",
            "Zeetex",
            "АШК",
            "Барнаул",
            "Белшина",
            "Воронеж",
            "Дніпрошина",
            "Кenda",
            "КШЗ",
            "Кама",
            "Красноярск",
            "МШЗ",
            "НИИШП",
            "Омскшина",
            "Петрошина",
            "Уралшина",
            "ЯШЗ"
        ];
    }

}