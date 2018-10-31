<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 13.10.2018
 * Time: 15:48
 */

namespace AppBundle\Command;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DeleteDublicateCardsCommand extends Command
{

    protected $em;

    protected $container;

    public function __construct(EntityManagerInterface $em, ContainerInterface $container)
    {
        $this->em = $em;
        $this->container = $container;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('cards:normalize')
            ->setDescription('normalize cars');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $query = $this->em->getConnection()->createQueryBuilder();

        $duplicates = $query->select('eaisto_number, COUNT(id) AS duplicatesCount')
            ->from('card')
            ->groupBy('eaisto_number')
            ->having('COUNT(id) > 1')
            ->where("eaisto_number IS NOT NULL AND eaisto_number != ''")
            ->execute()
            ->fetchAll(\PDO::FETCH_ASSOC);

        if (count($duplicates) > 0) {
            $eNumbers = implode(', ', array_map(function($el) {
                return $el['eaisto_number'];
            }, $duplicates));

            $cards = $this->em->getConnection()->createQueryBuilder()
                ->select('id, eaisto_number')
                ->from('card')
                ->where('eaisto_number IN (' . $eNumbers . ')')
                ->execute()
                ->fetchAll(\PDO::FETCH_ASSOC);

            $cardsNormalized = [];

            foreach ($cards as $card) {
                $cardsNormalized[$card['eaisto_number']] = $card;
            }

            $idsToSave = array_map(function($el) {
                return $el['id'];
            }, $cardsNormalized);

            foreach ($cards as $card) {
                if (!in_array($card['id'], $idsToSave)) {
                    $this->em->getConnection()->createQueryBuilder()
                        ->delete('card')
                        ->where('id = ' . $card['id'])
                        ->execute();

                    echo 'DELETED DUPLICATE CARD WITH ID ' . $card['id'] . PHP_EOL;
                }
            }
        } else {
            echo 'NO DUPLICATES FOUND';
        }
    }

}