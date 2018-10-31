<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 01.02.18
 * Time: 8:41
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class CarMarkRepository extends EntityRepository
{

    public function findAllMarks($q)
    {
        $query = $this->getEntityManager()->getConnection()->createQueryBuilder();

        $query->select('id_car_mark AS id, name')
            ->from('car_mark');

        if (!is_null($q)) {
            $query->andWhere('name LIKE "%:q%"')
                ->setParameter(':q', $q);
        }

        $result = $query->execute()
            ->fetchAll(\PDO::FETCH_ASSOC);

        return $result;
    }

    public function findAllModelsByMarkId($markId)
    {
        $query = $this->getEntityManager()->getConnection()->createQueryBuilder();

        $result = $query->select('id_car_model AS id, name')
            ->from('car_model')
            ->where('id_car_mark = :markId')
            ->setParameter(':markId', $markId)
            ->execute()
            ->fetchAll(\PDO::FETCH_ASSOC);

        return $result;
    }

}