<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 11.02.18
 * Time: 11:19
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class FuelTypeRepository extends EntityRepository
{

    public function findAll()
    {
        return $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('slug AS Code, name AS Title')
            ->from('fuel_type')
            ->execute()
            ->fetchAll(\PDO::FETCH_ASSOC);
    }

}