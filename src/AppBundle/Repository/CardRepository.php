<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 29.01.18
 * Time: 7:24
 */

namespace AppBundle\Repository;


use AppBundle\Service\TreeResolver;
use Doctrine\ORM\EntityRepository;
use PhpOffice\PhpSpreadsheet\Calculation\DateTime;

class CardRepository extends EntityRepository
{

    public function getRelationshipMapResolver()
    {
        $query = $this->getEntityManager()->getConnection()->createQueryBuilder();

        $allIds = $query->select('id, parent_id')
            ->from('users', 'u')
            ->execute()->fetchAll(\PDO::FETCH_ASSOC);// return $allIds;

        return new TreeResolver($allIds);
    }

    public function findSelfAndChildIds($currentUserId, $userId)
    {
        $query = $this->getEntityManager()->getConnection()->createQueryBuilder();

        $allIds = $query->select('id, parent_id')
            ->from('users', 'u')
            ->execute()->fetchAll(\PDO::FETCH_ASSOC);

        $idsNeeded = [];

        foreach ($allIds as $idsToAnalyze) {
            if (is_null($userId)) {
                if ($idsToAnalyze['id'] == $currentUserId || $idsToAnalyze['parent_id'] == $currentUserId) {
                    $idsNeeded[] = $idsToAnalyze['id'];
                }
            } else {
                if ($idsToAnalyze['id'] == $userId || $idsToAnalyze['parent_id'] == $userId) {
                    $idsNeeded[] = $idsToAnalyze['id'];
                }
            }
        }

        for ($level = 0; $level < 7; $level++) {
            foreach ($idsNeeded as $idNeeded) {
                foreach ($allIds as $idsToAnalyze) {
                    if ($idNeeded != $idsToAnalyze['id'] && $idsToAnalyze['parent_id'] == $idNeeded && !in_array($idsToAnalyze['id'], $idsNeeded)) {
                        $idsNeeded[] = $idsToAnalyze['id'];
                    }
                }
            }
        }

        return $idsNeeded;
    }

    public function findCardsFiltered($currentUserId, $userId = null, $dateFrom = null, $dateTill = null, $limit = 10, $offset = 0)
    {

        if (is_null($dateFrom)) {
            $dateFrom = (new \DateTime())->modify('first day of this month')->modify('midnight')->getTimestamp();
        } else {
            $dateFrom = (new \DateTime())->setTimestamp($dateFrom)->modify('midnight')->getTimestamp();
        }

        if (is_null($dateTill)) {
            $dateTill = time() + 999999999999;
        } else {
            $dateTill = (new \DateTime())->setTimestamp($dateTill)->modify('tomorrow')->getTimestamp();
        }

        $db = $this->getEntityManager()->getConnection();

        $query = $db->createQueryBuilder()
            ->select('c.id, c.property_type, c.owner_first_name, c.owner_last_name, c.owner_middle_name, c.comment,
                c.registration_number, c.vin, cmark.name as car_mark, cmodel.name as car_model, c.category_id, c.eaisto_number, c.eaisto_date,
                c.status, c.valid_till, c.created_at, c.issued_at, c.user_id, ccategory.letter, ccategory.code, users.login, users.name, users.price_a,
                users.price_b, users.price_c, users.price_d, users.price_e
            ')
            ->from('card', 'c')
            ->join('c', 'car_mark', 'cmark', 'c.car_mark_id = cmark.id_car_mark')
            ->join('c', 'car_model', 'cmodel', 'c.car_model_id = cmodel.id_car_model')
            ->join('c', 'category', 'ccategory', 'c.category_id = ccategory.id')
            ->join('c', 'users', 'users', 'c.user_id = users.id');

        $query = $query->addOrderBy('c.created_at', 'DESC');

        $query->andWhere('c.created_at >= :dateFrom AND c.created_at <= :dateTill')
            ->setParameter(':dateFrom', $dateFrom)
            ->setParameter(':dateTill', $dateTill);

        if ($dateFrom == $dateTill) {
            $query->setParameter(':dateTill', $dateTill + 9999999999999);
        }

//        if (!is_null($userId)) {
//            $query->andWhere('c.user_id = :id')
//                ->setParameter(':id', $userId);
//        } else {
//            $query->join('c', 'users', 'u', 'c.user_id = u.id AND (u.id = :currentUserId OR u.parent_id = :currentUserId)')
//                ->setParameter(':currentUserId', $currentUserId);
//        }

        $ids = $this->findSelfAndChildIds($currentUserId, $userId);

        $query->andWhere('c.user_id IN (' . implode(',', $ids) . ')');

        $countQuery = clone $query;

        $countTotal = count($countQuery->execute()->fetchAll(\PDO::FETCH_ASSOC));

        $query->setMaxResults($limit)
            ->setFirstResult($offset);

        $cards = $query->execute()->fetchAll(\PDO::FETCH_ASSOC);

        if (!is_null($userId)) {
            // just set this user's name to all card
            $user = $db->createQueryBuilder()
                ->select('u.id, u.login, u.name')
                ->from('users', 'u')
                ->where('u.id = :userId')
                ->setParameter(':userId', $userId)
                ->execute()
                ->fetch(\PDO::FETCH_ASSOC);

            foreach ($cards as & $card) {
                $card['user_id'] = $user['id'];
                $card['name'] = $user['name'];
                $card['login'] = $user['login'];
            }
        } else {
            $mapResolver = $this->getRelationshipMapResolver();
            $userRelationshipsChain = $mapResolver->findNode($currentUserId);

            $cardsAuthorsMapping = [];

            foreach ($cards as $card) {
                foreach ($userRelationshipsChain as $childNodeId => $childNodeValues) {
                    if ($mapResolver->containsNode($childNodeValues, (int) $card['user_id'])) {
                        $cardsAuthorsMapping[(int) $card['id']] = $childNodeId;
                    }

                    if ($childNodeId == $card['user_id']) {
                        $cardsAuthorsMapping[(int) $card['id']] = $childNodeId;
                    }

                    if ($card['user_id'] == $currentUserId) {
                        $cardsAuthorsMapping[(int) $card['id']] = $currentUserId;
                    }
                }
            }

            if ($cardsAuthorsMapping) {
                $users = $db->createQueryBuilder()
                    ->select('u.id, u.login, u.name')
                    ->from('users', 'u')
                    ->where('u.id IN (' . implode(',', array_unique($cardsAuthorsMapping)) . ')')
                    ->execute()
                    ->fetchAll(\PDO::FETCH_ASSOC);
            } else {
                $users = [];
            }

            foreach ($cards as & $card) {
                foreach ($users as $user) {
                    if ($user['id'] == $cardsAuthorsMapping[$card['id']]) {
                        $card['user_id'] = $user['id'];
                        $card['name'] = $user['name'];
                        $card['login'] = $user['login'];
                    }
                }
            }
        }

        $result['cards'] = $cards;
        $result['countTotal'] = $countTotal;

        return $result;
    }

    public function findStat($currentUserId, $userId = null, $dateFrom = null, $dateTill = null)
    {
        if (is_null($dateFrom)) {
            $dateFrom = (new \DateTime())->modify('first day of this month')->modify('midnight')->getTimestamp();
        } else {
            $dateFrom = (new \DateTime())->setTimestamp($dateFrom)->modify('midnight')->getTimestamp();
        }

        if (is_null($dateTill)) {
            $dateTill = time() + 999999999999;
        } else {
            $dateTill = (new \DateTime())->setTimestamp($dateTill)->modify('tomorrow')->getTimestamp();
        }

        $db = $this->getEntityManager()->getConnection();

        $query = $db->createQueryBuilder()
            ->select('u.id, u.is_active AS isActive, u.parent_id AS parentId, u.name, u.login, u.price_a AS priceA, u.price_b AS priceB, u.price_c AS priceC, u.price_d AS priceD, u.price_e AS priceE,
                COUNT(DISTINCT(ca.id)) as countA, COUNT(DISTINCT(cb.id)) as countB, COUNT(DISTINCT(cc.id)) as countC,
                COUNT(DISTINCT(cd.id)) as countD, COUNT(DISTINCT(ce.id)) as countE
            ')
            ->from('users', 'u')
            ->leftJoin('u', 'card', 'ca', 'u.id = ca.user_id AND ca.category_id IN (1) AND ca.created_at >= :dateFrom AND ca.created_at <= :dateTill')
            ->leftJoin('u', 'card', 'cb', 'u.id = cb.user_id AND cb.category_id IN (2, 3) AND cb.created_at >= :dateFrom AND cb.created_at <= :dateTill')
            ->leftJoin('u', 'card', 'cc', 'u.id = cc.user_id AND cc.category_id IN (4, 5) AND cc.created_at >= :dateFrom AND cc.created_at <= :dateTill')
            ->leftJoin('u', 'card', 'cd', 'u.id = cd.user_id AND cd.category_id IN (6, 7) AND cd.created_at >= :dateFrom AND cd.created_at <= :dateTill')
            ->leftJoin('u', 'card', 'ce', 'u.id = ce.user_id AND ce.category_id IN (8, 9, 10, 11) AND ce.created_at >= :dateFrom AND ce.created_at <= :dateTill')
            ->setParameter(':dateFrom', $dateFrom)
            ->setParameter(':dateTill', $dateTill);

        if ($dateFrom == $dateTill) {
            $query->setParameter(':dateTill', $dateTill + 9999999999999);
        }

        $ids = $this->findSelfAndChildIds($currentUserId, $userId);

        $query->andWhere('u.id IN (' . implode(',', $ids) . ')');

//        $query->andWhere('u.id = :currentUserId OR u.parent_id = :currentUserId')
//            ->setParameter(':currentUserId', $currentUserId);
//
//        if (!is_null($userId)) {
//            $query->andWhere('u.id = :id')
//                ->setParameter(':id', $userId);
//        }


        $query->addGroupBy('u.id');

        $result = $query->execute()->fetchAll(\PDO::FETCH_ASSOC);

        $resultGrouped = [];

        foreach ($result as $row) {
            if (is_null($userId)) {
                if ($row['id'] == $currentUserId || $row['parentId'] == $currentUserId) {
                    $resultGrouped[] = $row;
                }
            } else {
                if ($row['id'] == $userId || $row['parentId'] == $userId) {
                    $resultGrouped[] = $row;
                }
            }
        }

        foreach ($resultGrouped as & $rowGrouped) {
            foreach ($result as $row) {
                if ($rowGrouped['id'] != $row['id'] && $rowGrouped['id'] == $row['parentId'] && !in_array($row['id'], array_column($resultGrouped, 'id'))) {
                    
                    $rowGrouped['children'][] = $row['id'];
                    $rowGrouped['countA'] += $row['countA'];
                    $rowGrouped['countB'] += $row['countB'];
                    $rowGrouped['countC'] += $row['countC'];
                    $rowGrouped['countD'] += $row['countD'];
                    $rowGrouped['countE'] += $row['countE'];
                }
            }
        }

        foreach ($resultGrouped as & $rowGrouped) {
            foreach ($result as $row) {
                foreach ($rowGrouped['children'] as $childRow) {
                    if ($rowGrouped['id'] != $childRow && $childRow == $row['parentId'] && !in_array($row['id'], array_column($resultGrouped, 'id'))) {

                        $rowGrouped['countA'] += $row['countA'];
                        $rowGrouped['countB'] += $row['countB'];
                        $rowGrouped['countC'] += $row['countC'];
                        $rowGrouped['countD'] += $row['countD'];
                        $rowGrouped['countE'] += $row['countE'];
                    }
                }
            }
        }

        return $resultGrouped;
    }

}