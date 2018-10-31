<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 21.02.18
 * Time: 14:09
 */

namespace AppBundle\Service;


use AppBundle\Entity\Card;
use AppBundle\Entity\DCInterface;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Soap
{

    const EXPERT = 1;

    const OPERATOR = 2;

    protected $soapUser;

    protected $soapClient;

    protected $container;

    public function __construct(SoapUserInterface $soapUser, ContainerInterface $container, $regNumber)
    {
        $this->soapUser = $soapUser;
        $this->container = $container;

        if (!in_array($soapUser->getType(), [
            static::EXPERT,
            static::OPERATOR
        ])) {
            throw new UnknownSoapUserTypeException('Unknown type');
        }

        ini_set('soap.wsdl_cache_enabled', 0);
        ini_set('soap.wsdl_cache_ttl', 0);

        try {
            $this->soapClient = new \SoapClient(
                $this->getSoapHost($soapUser->getType()),
                $this->getProxyServer($regNumber)
            );

        } catch (\SoapFault $e) {
            //return $e;
            throw new SoapTimeOutException();
        }


    }

    public function registerCard(DCInterface $card)
    {
        return $this->call('RegisterCard', $card->toArray());
    }

    public function getCardByVin(Card $card)
    {
        $data = [
            'Purpose' => 'ForDublicate',
        ];

        //$data['vin'] = 'WF0LXXGBVLTB18217';

        if (!empty($card->getVin()) && $card->getVin() !== 'ОТСУТСТВУЕТ') {
            $data['vin'] = $card->getVin();
        }

        if (!empty($card->getRegistrationNumber()) && $card->getRegistrationNumber() !== 'ОТСУТСТВУЕТ') {
            $data['regNumber'] = $card->getRegistrationNumber();
        }

        if (!empty($card->getBodyNumber()) && $card->getBodyNumber() !== 'ОТСУТСТВУЕТ') {
            $data['BodyNumber'] = $card->getBodyNumber();
        }

        if (!empty($card->getFrameNumber()) && $card->getFrameNumber() !== 'ОТСУТСТВУЕТ') {
            $data['FrameNumber'] = $card->getFrameNumber();
        }

        return $this->call('GetCardByVin', $data);
    }

    public function editCard(Card $card)
    {
        $eaistoCard = $card->toArray();

        $eaistoCard['card']['Id'] = $card->getEaistoId();
        $eaistoCard['card']['Form']['Number'] = $card->getEaistoNumber();
        $eaistoCard['card']['Fuel'] = $card->getFuelType();

        return $this->call('ChangeCard', $eaistoCard);
    }

    public function cancelCard(Card $card)
    {

    }

    protected function call($serviceName, $arguments)
    {
        $arguments['user'] = $this->soapUser->getCredentials();

        try {
            $result = $this->soapClient->__soapCall($serviceName, [
                $arguments
            ]);

            return $result;
        } catch (\SoapFault $e) {
            return $e;
        }
    }

    protected function getSoapHost($type)
    {
        if ($this->container->getParameter('kernel.environment') === 'dev') {
            $hosts = [
//                static::EXPERT => 'http://eaistotest.srvdev.ru/common/ws/arm_expert.php?wsdl',
//                static::OPERATOR => 'http://eaistotest.srvdev.ru/common/ws/arm_operator.php?wsdl'
                static::EXPERT => 'https://eaisto.gibdd.ru/common/ws/arm_expert.php?wsdl',
                static::OPERATOR => 'https://eaisto.gibdd.ru/common/ws/arm_operator.php?wsdl'
            ];

            return $hosts[$type];
        }

        $hosts = [
            static::EXPERT => 'https://eaisto.gibdd.ru/common/ws/arm_expert.php?wsdl',
            static::OPERATOR => 'https://eaisto.gibdd.ru/common/ws/arm_operator.php?wsdl'
        ];

        return $hosts[$type];
    }

    protected function getProxyServer($regNumber)
    {
        $servers = [
            '09115' => [
                'proxy_host'     => '46.8.107.198',
                'proxy_port'     => 1050,
                'proxy_login'    => 'Uw1Nq3Y2',
                'proxy_password' => '8afXVWGi',
                'exceptions' => true
            ],
            '08870' => [
                'proxy_host'     => '46.8.107.198',
                'proxy_port'     => 1050,
                'proxy_login'    => 'Uw1Nq3Y2',
                'proxy_password' => '8afXVWGi',
                'exceptions' => true
            ],
            '08489' => [
                'proxy_host'     => '188.130.188.157',
                'proxy_port'     => 1050,
                'proxy_login'    => 'EXyTgkRi',
                'proxy_password' => 'epbc1fNJ',
                'exceptions' => true
            ],
            '00429' => [
                'proxy_host'     => '188.130.188.157',
                'proxy_port'     => 1050,
                'proxy_login'    => 'EXyTgkRi',
                'proxy_password' => 'epbc1fNJ',
                'exceptions' => true
            ],
        ];

        return $servers[$regNumber] ?? null;
    }

}