<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 27.09.2018
 * Time: 7:33
 */

namespace AppBundle\Validator;


use AppBundle\Entity\Category;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DCValidator
{

    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function validateCard($data)
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

        if (empty($data['carMarkName'])) {
            $errors['carMarkName'][] = 'Марка авто должна быть указана';
        }

        if (empty($data['carModelName'])) {
            $errors['carModelName'][] = 'Модель авто должна быть указана';
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

        if (empty($data['registrationNumber'])) {
            $errors['registrationNumber'][] = 'Нажмите "Отсутствует", если Рег. номера рамы нет';
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

    public function validateIdentity($vin, $bodyNumber, $frameNumber)
    {
        return !(((empty($vin)) || $vin == 'ОТСУТСТВУЕТ') && (empty($bodyNumber) || $bodyNumber == 'ОТСУТСТВУЕТ') && (empty($frameNumber) || $frameNumber == 'ОТСУТСТВУЕТ'));
    }

    public function validateProperRegistrationNumber($categoryId, $registrationNumber, $unusual = false)
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
        $category = $this->container->get('doctrine')->getRepository(Category::class)->find($categoryId);
        $categoryLetter = $category->getLetter();

        $properRegistrationNumber = preg_match($patterns[$categoryLetter], $registrationNumber);

        return $properRegistrationNumber;
    }

    public function validateProperVin($vin)
    {
        if ($vin == 'ОТСУТСТВУЕТ') {
            return true;
        }

        return preg_match('#^[0123456789ABCDEFGHJKLMNPRSTUVWXYZ]{17}$#', $vin);
    }

}