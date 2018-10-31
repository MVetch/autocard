<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 26.09.2018
 * Time: 18:20
 */

namespace AppBundle\Repository;


use Doctrine\ORM\EntityRepository;

class ApplicationRepository extends EntityRepository
{

    public function findApplicationsFiltered($dateFrom = null, $dateTill = null, $limit = 10, $offset = 0)
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
            ->select('a.id, a.created_at AS createdAt, a.registration_number AS registrationNumber,
                    a.vin, a.email, a.eaisto_number AS eaistoNumber, a.eaisto_status AS eaistoStatus,
                    a.purchased, a.type, a.photo1, a.photo2
            ')
            ->from('application', 'a')
            ->addOrderBy('a.created_at', 'DESC')
            ->andWhere('a.created_at >= :dateFrom AND a.created_at <= :dateTill')
            ->setParameter(':dateFrom', $dateFrom)
            ->setParameter(':dateTill', $dateTill);

        if ($dateFrom == $dateTill) {
            $query->setParameter(':dateTill', $dateTill + 9999999999999);
        }

        $countQuery = clone $query;

        $countTotal = count($countQuery->execute()->fetchAll(\PDO::FETCH_ASSOC));

        $query->setMaxResults($limit)
            ->setFirstResult($offset);

        $result = $query->execute()->fetchAll(\PDO::FETCH_ASSOC);

        return [
            'countTotal' => $countTotal,
            'data' => $result
        ];
    }

}