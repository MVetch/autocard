<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 04.02.18
 * Time: 15:24
 */

namespace AppBundle\Repository;


use AppBundle\Entity\Operator;
use Doctrine\ORM\EntityRepository;

class LimitRepository extends EntityRepository
{

    public function findByUserId($userId)
    {
        $query = $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select("l.id, l.working_hours AS workingHours")
            ->from('limits', 'l')
            ->where('l.user_id = :userId')
            ->setParameter(':userId', $userId);

        $limits = $query
            ->execute()
            ->fetchAll(\PDO::FETCH_ASSOC);

        $workingHoursDecoded = $this->extractWorkingHours($limits);

        $result = [];

        foreach ($workingHoursDecoded as &$workingHour) {
            $hours = [];

            foreach ($workingHour['workingHours'] as $item) {
                $hours[] = "'" . ((int) $item['day'] + 2) . '-' . $item['hour'] . "'";
            }

            if (empty($hours)) {
                $hours[] = '0-0';
            }

            $queryCardsCount = $this->getEntityManager()->getConnection()->createQueryBuilder()
                ->select("l.id, l.name, l.limitation, l.is_active AS isActive,
                GROUP_CONCAT(DISTINCT(c.letter) SEPARATOR '') AS categoryGroups,
                COUNT(DISTINCT(card.id)) AS countCards, l.working_hours AS workingHours
            ")
                ->from('limits', 'l')
                ->leftJoin('l', 'category_limit', 'cl', 'l.id = cl.limit_id')
                ->leftJoin('cl', 'category', 'c', 'cl.category_id = c.id')
                ->leftJoin('l', 'operator_limit', 'ol', 'l.id = ol.limit_id')
                ->leftJoin('l', 'user_limit', 'ul', 'l.id = ul.limit_id')
                ->leftJoin('l', 'card', 'card', "card.operator_id = ol.operator_id 
                    AND card.user_id = ul.user_id AND card.category_id = cl.category_id
                    AND CONCAT(DAYOFWEEK(FROM_UNIXTIME(card.created_at)), '-', HOUR(FROM_UNIXTIME(card.created_at))) IN (" . implode(",", $hours  ) . ")        
                    AND DAYOFMONTH(FROM_UNIXTIME(card.created_at)) = DAYOFMONTH(NOW())
                    AND MONTH(FROM_UNIXTIME(card.created_at)) = MONTH(NOW())
                    AND YEAR(FROM_UNIXTIME(card.created_at)) = YEAR(NOW())
                ")
                ->andWhere('l.id = :limitId')
                ->setParameter(':limitId', $workingHour['limitId'])
                ->groupBy('l.id');

            $result[] = $queryCardsCount
                ->execute()
                ->fetch(\PDO::FETCH_ASSOC);

        }

        return $result;
    }

    public function checkLimits()
    {
//        $cardsResult = $this->getEntityManager()->getConnection()->createQueryBuilder()
//            ->select('COUNT(card.id) AS total, card.operator_id AS operatorId')
//            ->from('card', 'card')
//            ->where('DAYOFMONTH(FROM_UNIXTIME(card.created_at)) = DAYOFMONTH(NOW())')
//            ->andWhere('MONTH(FROM_UNIXTIME(card.created_at)) = MONTH(NOW())')
//            ->andWhere('YEAR(FROM_UNIXTIME(card.created_at)) = YEAR(NOW())')
//            ->groupBy('card.operator_id')
//            ->execute()
//            ->fetchAll(\PDO::FETCH_ASSOC);

//        $applicationsResult = $this->getEntityManager()->getConnection()->createQueryBuilder()
//            ->select('COUNT(card.id) AS total, card.operator_id AS operatorId')
//            ->from('application', 'card')
//            ->where('DAYOFMONTH(FROM_UNIXTIME(card.created_at)) = DAYOFMONTH(NOW())')
//            ->andWhere('MONTH(FROM_UNIXTIME(card.created_at)) = MONTH(NOW())')
//            ->andWhere('YEAR(FROM_UNIXTIME(card.created_at)) = YEAR(NOW())')
//            ->andWhere('card.eaisto_status = 2')
//            ->groupBy('card.operator_id')
//            ->execute()
//            ->fetchAll(\PDO::FETCH_ASSOC);

        $cardsResult = $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('COUNT(card.id) AS total, o.id AS operatorId')
            ->from('operator', 'o')
            ->leftJoin(
                'o',
                'card',
                'card',
                'o.id = card.operator_id 
                AND DAYOFMONTH(FROM_UNIXTIME(card.created_at)) = DAYOFMONTH(NOW())
                AND MONTH(FROM_UNIXTIME(card.created_at)) = MONTH(NOW())
                AND YEAR(FROM_UNIXTIME(card.created_at)) = YEAR(NOW())
                '
            )
            ->groupBy('o.id')
            ->execute()
            ->fetchAll(\PDO::FETCH_ASSOC);

        $applicationsResult = $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select('COUNT(card.id) AS total, o.id AS operatorId')
            ->from('operator', 'o')
            ->leftJoin(
                'o',
                'application',
                'card',
                'o.id = card.operator_id 
                AND DAYOFMONTH(FROM_UNIXTIME(card.created_at)) = DAYOFMONTH(NOW())
                AND MONTH(FROM_UNIXTIME(card.created_at)) = MONTH(NOW())
                AND YEAR(FROM_UNIXTIME(card.created_at)) = YEAR(NOW())
                AND card.eaisto_status = 2
                '
            )
            ->groupBy('o.id')
            ->execute()
            ->fetchAll(\PDO::FETCH_ASSOC);

        $result = [];

        foreach ($applicationsResult as $application) {
            foreach ($cardsResult as $card) {
                if ($application['operatorId'] == $card['operatorId']) {
                    $result[] = [
                        'total' => $application['total'] + $card['total'],
                        'operatorId' => $application['operatorId']
                    ];
                }
            }
        }

        foreach ($result as & $item) {
//            if ($item['operatorId'] == 5) {
//                $item['available'] = $item['total'] < 63;
//            } else {
//                $item['available'] = $item['total'] < 63;
//            }

            $item['available'] = $item['total'] < 63;
        }

        return $result;
    }

    private function extractWorkingHours($limitData)
    {
        $result = [];

        foreach ($limitData as $limit) {
            $result[] = [
                'limitId' => $limit['id'],
                'workingHours' => json_decode($limit['workingHours'], true)
            ];
        }

        return $result;
    }

    public function findGroupCategories($limitId)
    {
        return $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select("c.letter AS id, c.letter_description AS title")
            ->from('category_limit', 'cl')
            ->join('cl', 'category', 'c', 'c.id = cl.category_id')
            ->where('cl.limit_id = :limitId')
            ->setParameter(':limitId', $limitId)
            ->addGroupBy('id')
            ->addGroupBy('title')
            ->execute()
            ->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findUsers($limitId)
    {
        return $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select("u.id AS id, CONCAT(u.name, ' (', u.login, ')') as title")
            ->from('user_limit', 'ul')
            ->join('ul', 'users', 'u', 'u.id = ul.user_id')
            ->where('ul.limit_id = :limitId')
            ->setParameter(':limitId', $limitId)
            ->addGroupBy('id')
            ->addGroupBy('title')
            ->execute()
            ->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findOperators($limitId)
    {
        return $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select("o.id AS id, o.short_name as title")
            ->from('operator_limit', 'ol')
            ->join('ol', 'operator', 'o', 'o.id = ol.operator_id')
            ->where('ol.limit_id = :limitId')
            ->setParameter(':limitId', $limitId)
            ->addGroupBy('id')
            ->addGroupBy('title')
            ->execute()
            ->fetchAll(\PDO::FETCH_ASSOC);
    }

}