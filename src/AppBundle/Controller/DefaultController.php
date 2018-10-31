<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Application;
use AppBundle\Entity\BrakeType;
use AppBundle\Entity\Card;
use AppBundle\Entity\FuelType;
use AppBundle\Entity\User;
use AppBundle\Service\PDFService\Presenter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter\Column;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\SecurityBundle\Tests\Functional\Bundle\AclBundle\Entity\Car;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/{reactRouting}", requirements={"reactRouting"="(((stat)|(cards)|(card)|(limit)|(limits)|(user)|(users)|(feed)|(settings))(\/.+)?)|()"})
     */
    public function indexAction(Request $request)
    {// replace this example code with whatever you need
        return $this->render('default/index.html');
    }

    /**
     * @Route("/export/pdf", name="pdf")
     */
    public function pdfAction(Request $request)
    {
        $presenter = new Presenter();

        if ($request->query->get('id')) {
            /**
             * @var Card $card
             */
            $card = $this->getDoctrine()->getRepository(Card::class)->find($request->query->get('id'));
            $info['fuelType'] = $this->getDoctrine()->getRepository(FuelType::class)->findOneBy(['slug' => $card->getFuelType()])->getName();
            $info['brakingSystem'] = $this->getDoctrine()->getRepository(BrakeType::class)->findOneBy(['slug' => $card->getBrakingSystem()])->getName();

            if ($card) {
                return $presenter->present($card, $request->query->get('type'), $info);
            } else {
                return new JsonResponse(['success' => false]);
            }
        }

        return new JsonResponse(['success' => false]);
    }

    /**
     * @Route("/export/application_pdf", name="application_pdf")
     */
    public function applicationPdfAction(Request $request)
    {
        $presenter = new Presenter();

        if ($request->query->get('id')) {
            /**
             * @var Card $card
             */
            $card = $this->getDoctrine()->getRepository(Application::class)->find($request->query->get('id'));
            $info['fuelType'] = $this->getDoctrine()->getRepository(FuelType::class)->findOneBy(['slug' => $card->getFuelType()])->getName();
            $info['brakingSystem'] = $this->getDoctrine()->getRepository(BrakeType::class)->findOneBy(['slug' => $card->getBrakingSystem()])->getName();

            if ($card) {
                return $presenter->present($card, $request->query->get('type'), $info);
            } else {
                return new JsonResponse(['success' => false]);
            }
        }

        return new JsonResponse(['success' => false]);
    }

    /**
     * @Route("/export/relations", name="relations_test")
     */
    public function testRelationshipsAction(Request $request)
    {
        $result = $this->getDoctrine()->getRepository(Card::class)->getRelationshipMap();

        return new JsonResponse([
            'data' => $result
        ]);
    }

    /**
     * @Route("/api/export/excel", name="excel")
     */
    public function excelAction(Request $request) {

        $this->denyAccessUnlessGranted('ROLE_USER', null, 'no access');

        /**
         * @var User $currentUser
         */
        //$currentUser = $this->getDoctrine()->getRepository(User::class)->find(1);
        $currentUser = $this->get('security.token_storage')->getToken()->getUser();

        $userId = $request->query->get('userId') ?? null;
        $dateFrom = $request->query->get('dateFrom') ?? null;
        $dateTill = $request->query->get('dateTill') ?? null;
        $limit = 10000;
        $offset = 0;

        $result = $this->getDoctrine()->getRepository(Card::class)->findCardsFiltered($currentUser->getId(), $userId, $dateFrom, $dateTill, $limit, $offset);

        //dump($result);die();

        $spreadsheet = new Spreadsheet();

        $spreadsheet->getProperties()->setCreator('Maarten Balliauw')
            ->setLastModifiedBy('Maarten Balliauw')
            ->setTitle('PhpSpreadsheet Test Document')
            ->setSubject('PhpSpreadsheet Test Document')
            ->setDescription('Test document for PhpSpreadsheet, generated using PHP classes.')
            ->setKeywords('office PhpSpreadsheet php')
            ->setCategory('Test result file');

        $spreadsheet->setActiveSheetIndex(0);
        $spreadsheet->getActiveSheet()->setCellValue('A2', '№')
            ->setCellValue('B2', 'ЕАИСТО')
            ->setCellValue('C2', 'ДАТА')
            ->setCellValue('D2', 'Собственник')
            ->setCellValue('E2', 'Марка')
            ->setCellValue('F2', 'Модель')
            ->setCellValue('G2', 'VIN')
            ->setCellValue('H2', 'Гос. номер')
            ->setCellValue('I2', 'Кат.')
            ->setCellValue('J2', 'Цена')
            ->setCellValue('K2', 'Агент')
            ->setCellValue('L2', 'Логин')
            ->setCellValue('M2', 'Комментарий');

        $spreadsheet->getActiveSheet()->getStyle('A2:M2')->getFont()->setBold(true);
        $spreadsheet->getActiveSheet()->getStyle('A2:M2')->getAlignment()->setWrapText(true);

        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(25.5);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(18.5);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(30.5);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(16.5);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(16.5);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(27.5);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(22.5);
        $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(10.5);
        $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(24.5);
        $spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(24.5);
        $spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(30.5);

        $number = 2;

        foreach ($result['cards'] as $item) {
            $columnNumber = $number + 1;
            $middleName = ($item['owner_middle_name'] != 'ОТСУТСТВУЕТ') ? $item['owner_middle_name'] : null;

            $createdAt = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($item['created_at']);

            $price = $item['price_a'];

            if ($item['letter'] == 'B') {
                $price = $item['price_b'];
            }

            if ($item['letter'] == 'C') {
                $price = $item['price_c'];
            }

            if ($item['letter'] == 'D') {
                $price = $item['price_d'];
            }

            if ($item['letter'] == 'E') {
                $price = $item['price_e'];
            }

            $spreadsheet->getActiveSheet()->setCellValue("A{$columnNumber}", $number - 1);
            $spreadsheet->getActiveSheet()->setCellValueExplicit("B{$columnNumber}", $item['eaisto_number'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $spreadsheet->getActiveSheet()->setCellValue("C{$columnNumber}", $createdAt);
            $spreadsheet->getActiveSheet()->setCellValue("D{$columnNumber}", "{$item['owner_last_name']} {$item['owner_first_name']} {$middleName}");
            $spreadsheet->getActiveSheet()->setCellValue("E{$columnNumber}", $item['car_mark']);
            $spreadsheet->getActiveSheet()->setCellValueExplicit("F{$columnNumber}", $item['car_model'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $spreadsheet->getActiveSheet()->setCellValueExplicit("G{$columnNumber}", $item['vin'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $spreadsheet->getActiveSheet()->setCellValueExplicit("H{$columnNumber}", $item['registration_number'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $spreadsheet->getActiveSheet()->setCellValueExplicit("I{$columnNumber}", $item['letter'] . '/' . $item['code'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $spreadsheet->getActiveSheet()->setCellValue("J{$columnNumber}", $price);
            $spreadsheet->getActiveSheet()->setCellValueExplicit("K{$columnNumber}", $item['name'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $spreadsheet->getActiveSheet()->setCellValueExplicit("L{$columnNumber}", $item['login'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $spreadsheet->getActiveSheet()->setCellValueExplicit("M{$columnNumber}", $item['comment'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

            $spreadsheet->getActiveSheet()->getStyle("C{$columnNumber}")
                ->getNumberFormat()
                ->setFormatCode(
                    \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_YYYYMMDD2
                );

            $number++;
        }

        $spreadsheet->getActiveSheet()->setCellValue("J1", "=SUBTOTAL(9,J2:J{$number})");
        $spreadsheet->getActiveSheet()->setCellValue("I1", "Всего");
        //$retVal = $spreadsheet->getCell('J1')->getCalculatedValue();

        $spreadsheet->getActiveSheet()->setAutoFilter("A2:M{$number}");

//        // Set active filters
//        $autoFilter = $spreadsheet->getActiveSheet()->getAutoFilter();
//
//
//        // Filter the Country column on a filter value of Germany
//        //	As it's just a simple value filter, we can use FILTERTYPE_FILTER
//        $autoFilter->getColumn('B')
//            ->setFilterType(Column::AUTOFILTER_FILTERTYPE_FILTER);

        $fileName = 'stat' . time();

        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename=' . '"' . $fileName . '".xlsx');
        header('Cache-Control: max-age=0');

        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }
}
