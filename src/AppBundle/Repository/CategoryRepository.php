<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 29.01.18
 * Time: 7:58
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class CategoryRepository extends EntityRepository
{

    public function findAll()
    {
        $categories = $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('*')
            ->from('category')
            ->execute()
            ->fetchAll(\PDO::FETCH_ASSOC);

        return $categories;
    }

    public function findCategoryGroups()
    {
        $groups = $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('letter, letter_description as letterDescription')
            ->from('category')
            ->addGroupBy('letter')
            ->addGroupBy('letterDescription')
            ->execute()
            ->fetchAll(\PDO::FETCH_ASSOC);

        return $groups;
    }

}