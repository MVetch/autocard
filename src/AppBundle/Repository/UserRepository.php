<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 29.01.18
 * Time: 11:38
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{

    public function findAllByUserId($userId, $excludeCurrent = null)
    {
        $query = $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('id, login, parent_id AS parentId, city_id AS cityId, email, name, is_active AS isActive, 
            price_a AS priceA, price_b AS priceB, price_c AS priceC, price_d AS priceD, price_e AS priceE
            ')
            ->from('users');

        if ($excludeCurrent === 1) {
            $query->andWhere('parent_id = :id')
                ->setParameter(':id', $userId);
        } else {
            $query->andWhere('id = :id OR parent_id = :id')
                ->setParameter(':id', $userId);
        }

        $users = $query
            ->execute()
            ->fetchAll(\PDO::FETCH_ASSOC);

        return $users;
    }

    public function findCitiesByTerm($q = null)
    {
        $query = $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('id, name, ru_path as ruPath')
            ->from('cities');

        if (is_null($q)) {
            $query->setMaxResults(5);
        } else {
            $query->andWhere('name LIKE :q')
                ->setParameter(':q', "{$q}%")
                ->setMaxResults(100);
        }

        $query->addOrderBy('ru_type', 'ASC')
            ->addOrderBy('name', 'ASC');

        $cities = $query
            ->execute()
            ->fetchAll(\PDO::FETCH_ASSOC);

        return $cities;
    }

    public function findAgentInfo($id)
    {
        $query = $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('id, login, parent_id AS parentId, city_id AS cityId, email, name, is_active AS isActive, 
            price_a AS priceA, price_b AS priceB, price_c AS priceC, price_d AS priceD, price_e AS priceE, ips
            ')
            ->from('users')
            ->where('id = :id')
            ->setParameter(':id', $id);

        $user = $query
            ->execute()
            ->fetch(\PDO::FETCH_ASSOC);

        if ($user['ips']) {
            $user['ips'] = json_decode($user['ips']);
        }

        return $user;
    }

}