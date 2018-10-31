<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 01.10.2018
 * Time: 22:30
 */

namespace AppBundle\Command;

use AppBundle\Entity\Card;
use AppBundle\Entity\Expert;
use AppBundle\Service\Soap;
use AppBundle\Service\SoapUserInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AttemptRegisterCardCommand extends Command
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
            ->setName('cards:register')
            ->setDescription('Load fake cars');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
//        /**
//         * @var Card $card
//         */
//        $card = $this->em->getRepository(Card::class)
//            ->findOneBy([
//                'eaistoNumber' => null
//            ]);
//
//        dump($card);die();

        /**
         * @var Card $card
         */
        $card = $this->em->getRepository(Card::class)->findOneBy([
            'eaistoNumber' => null
            //'id' => 11760
        ]);

        if ($card) {
            /**
             * @var SoapUserInterface $expert
             */
            $expert = $this->em->getRepository(Expert::class)->find($card->getExpert());
            $operator = $card->getOperator();

            $soapService = new Soap($expert, $this->container, $operator->getRegNumber());

            $eaistoInfo = $soapService->getCardByVin($card);

            if (isset($eaistoInfo->GetCardByVinResult)) {

                if (is_array($eaistoInfo->GetCardByVinResult)) {
                    $latestEaisto = $eaistoInfo->GetCardByVinResult[0];
                } else {
                    $latestEaisto = $eaistoInfo->GetCardByVinResult;
                }

                if ((new \DateTime($latestEaisto->DateOfDiagnosis))->format('m-Y')
                    == (new \DateTime())->setTimestamp($card->getCreatedAt())->format('m-Y'))
                {
                    $eaistoId = $latestEaisto->Id;
                    $eaistoNumber = $latestEaisto->Form->Number;
                    $eaistoDate = time();

                    $card->setEaistoNumber($eaistoNumber);
                    $card->setEaistoId($eaistoId);
                    $card->setEaistoDate($eaistoDate);

                    $this->em->persist($card);
                    $this->em->flush();

                    echo 'FOUND CARD:' . PHP_EOL . PHP_EOL;

                    print_r([
                        'cardId' => $card->getId(),
                        'eaistoId' => $eaistoId,
                        'eaistoNumber' => $eaistoNumber,
                        'eaistoDate' => $eaistoDate
                    ]);
                } else {
                    $eaistoInfo = $soapService->registerCard($card);

                    if (!empty($eaistoInfo->Nomer)) {
                        $card->setEaistoNumber($eaistoInfo->Nomer);
                        $card->setEaistoId($eaistoInfo->RegisterCardResult);
                        $card->setEaistoDate(time());

                        $this->em->persist($card);
                        $this->em->flush();

                        echo 'REGISTERED CARD:' . PHP_EOL . PHP_EOL;

                        print_r([
                            'cardId' => $card->getId(),
                            'eaistoId' => $card->getEaistoId(),
                            'eaistoNumber' => $card->getEaistoNumber(),
                            'eaistoDate' => $card->getEaistoDate()
                        ]);
                    } else {
                        echo 'CARD ID: ' . $card->getId() . ' ' . $eaistoInfo->getMessage() . PHP_EOL;
                    }
                }
            } else {
                $eaistoInfo = $soapService->registerCard($card);

                if (!empty($eaistoInfo->Nomer)) {
                    $card->setEaistoNumber($eaistoInfo->Nomer);
                    $card->setEaistoId($eaistoInfo->RegisterCardResult);
                    $card->setEaistoDate(time());

                    $this->em->persist($card);
                    $this->em->flush();

                    echo 'REGISTERED CARD:' . PHP_EOL . PHP_EOL;

                    print_r([
                        'cardId' => $card->getId(),
                        'eaistoId' => $card->getEaistoId(),
                        'eaistoNumber' => $card->getEaistoNumber(),
                        'eaistoDate' => $card->getEaistoDate()
                    ]);
                } else {
                    echo 'CARD ID: ' . $card->getId() . ' ' . $eaistoInfo->getMessage() . PHP_EOL;
                }
            }
        } else {
            echo 'No unregistered cards found' . PHP_EOL;
        }
    }

}