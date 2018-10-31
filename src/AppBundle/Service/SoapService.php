<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 27.01.18
 * Time: 22:47
 */

namespace AppBundle\Service;


use AppBundle\Entity\Operator;

class SoapService
{

    protected $expertClient;

    protected $operatorClient;

    protected $credentials = [
        'user' => [
            'Name' => 'expert769012',
            'Password' => 'kRmsH9oZRf20JwHAw0H2'
        ]
    ];

    protected $connectionCredentials = [
        'dev_autocard' => [
            Operator::TECHAUTOPRO_NUMBER => [
                'user' => [
                    'Name' => 'expert1',
                    'Password' => '123456'
                ]
            ],
            Operator::BEZOPASNOST_NUMBER => [
                'user' => [
                    'Name' => 'expert2',
                    'Password' => '123456'
                ]
            ],
            Operator::LODAS_NUMBER => [
                'user' => [
                    'Name' => 'expert3',
                    'Password' => '123456'
                ]
            ]
        ],
        'dev_eaisto' => [
            Operator::TECHAUTOPRO_NUMBER => [
                'user' => [
                    'Name' => 'expert769012',
                    'Password' => 'kRmsH9oZRf20JwHAw0H2'
                ]
            ],
            Operator::BEZOPASNOST_NUMBER => [
                'user' => [
                    'Name' => 'expert769012',
                    'Password' => 'kRmsH9oZRf20JwHAw0H2'
                ]
            ],
            Operator::LODAS_NUMBER => [
                'user' => [
                    'Name' => 'expert769012',
                    'Password' => 'kRmsH9oZRf20JwHAw0H2'
                ]
            ]
        ],
        'production_eaisto' => [
            Operator::TECHAUTOPRO_NUMBER => [

            ],
            Operator::BEZOPASNOST_NUMBER => [

            ],
            Operator::LODAS_NUMBER => [

            ]
        ]
    ];

    protected $host;

    protected $env;

    public function __construct(Operator $operator, $env = 'dev_eaisto', $type = 'expert')
    {
        ini_set('soap.wsdl_cache_enabled', 0);
        ini_set('soap.wsdl_cache_ttl', 0);

        $this->env = $env;

        if ($env == 'dev_eaisto') {
            $this->host = "http://eaistotest.srvdev.ru/common/ws/arm_{$type}.php?wsdl";
        } elseif ($env == 'dev_autocard') {
            $this->host = '/soap?type=expert';
        } elseif ($env == 'production_eaisto') {
            $this->host = '/';
        }

        $this->credentials = $this->connectionCredentials[$env][$operator->getRegNumber()];

        try {
            $this->expertClient = new \SoapClient(
                $this->host
            );

        } catch (\SoapFault $e) {
            return $e;
        }
    }

    public function get(String $serviceName, Array $arguments = []) {
//        if ($this->env === 'dev_eaisto' || $this->env == 'dev_autocard') {
//            if ($serviceName == 'RegisterCard') {
//                return [
//                    'RegisterCardResult' => mt_rand(14568111, 14589999),
//                    'Nomer' => '00' . mt_rand(1500000000000, 1500000009999)
//                ];
//            }
//        }

        $arguments['user'] = $this->credentials['user'];

        try {
            $result = $this->expertClient->__soapCall($serviceName, array($arguments));

            return $result;
        } catch (\SoapFault $e) {
            return $e;
        }
    }

}