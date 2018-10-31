<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 29.01.18
 * Time: 20:22
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class OperatorRepository extends EntityRepository
{

    public function findAll()
    {
        $operators = $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('id, full_name AS fullName, short_name AS shortName, reg_number AS regNumber, is_active AS isActive')
            ->from('operator')
            ->where('is_active = 1')
            ->execute()
            ->fetchAll(\PDO::FETCH_ASSOC);

        return $operators;
    }

    public function findOperatorsCategories()
    {
        $data = $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('operator_id AS operatorId, category_id AS categoryId')
            ->from('operator_category')
            ->execute()
            ->fetchAll(\PDO::FETCH_ASSOC);

        return $data;
    }

}