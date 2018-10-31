<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 05.02.18
 * Time: 17:10
 */

namespace AppBundle\Service\PDFService;


use AppBundle\Entity\Card;
use AppBundle\Entity\DCInterface;

class Presenter
{

    public function present(DCInterface $card, $type = Generator::TYPE_COMMON, $info)
    {
        $generator = new Generator($card, $info);

        $operator = $card->getOperator();

        if ($operator->getRegNumber() == '08489') {
            if ($type == Generator::TYPE_COMMON) {
                $generator->printForm = Generator::TECHAUTOPRO_COMMON;
            } elseif ($type == Generator::TYPE_OWN_PRINT) {
                $generator->printForm = Generator::TECHAUTOPRO_OWN_PRINT;
            } else {
                $generator->printForm = Generator::TECHAUTOPRO_UNIVERSAL_PRINT;
            }
        } elseif ($operator->getRegNumber() == '00594') {
            if ($type == Generator::TYPE_COMMON) {
                $generator->printForm = Generator::BEZOPASNOST_COMMON;
            } elseif ($type == Generator::TYPE_OWN_PRINT) {
                $generator->printForm = Generator::BEZOPASNOST_OWN_PRINT;
            } else {
                $generator->printForm = Generator::BEZOPASNOST_UNIVERSAL_PRINT;
            }
        } elseif ($operator->getRegNumber() == '08870') {
            if ($type == Generator::TYPE_COMMON) {
                $generator->printForm = Generator::ISTMOBIL_COMMON;
            } elseif ($type == Generator::TYPE_OWN_PRINT) {
                $generator->printForm = Generator::ISTMOBIL_OWN_PRINT;
            } else {
                $generator->printForm = Generator::ISTMOBIL_UNIVERSAL_PRINT;
            }
        } elseif ($operator->getRegNumber() == '09115') {
            if ($type == Generator::TYPE_COMMON) {
                $generator->printForm = Generator::TECHEXPERT_COMMON;
            } elseif ($type == Generator::TYPE_OWN_PRINT) {
                $generator->printForm = Generator::TECHEXPERT_OWN_PRINT;
            } else {
                $generator->printForm = Generator::TECHEXPERT_UNIVERSAL_PRINT;
            }
        } else {
            if ($type == Generator::TYPE_COMMON) {
                $generator->printForm = Generator::LODAS_COMMON;
            } elseif ($type == Generator::TYPE_OWN_PRINT) {
                $generator->printForm = Generator::LODAS_OWN_PRINT;
            } else {
                $generator->printForm = Generator::LODAS_UNIVERSAL_PRINT;
            }
        }

        $generator->AliasNbPages();
        $generator->AddPage();
        $generator->createFirstPage();

        $generator->AddPage();
        $generator->createSecondPage();

        return $generator->Output();
    }

}