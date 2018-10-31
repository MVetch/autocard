<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 01.10.2018
 * Time: 20:36
 */

namespace AppBundle\Service;


use Symfony\Component\HttpKernel\Exception\HttpException;

class SoapTimeOutException extends HttpException
{

    public function __construct($message = null, \Exception $previous = null, $code = 0)
    {
        parent::__construct(200, $message, $previous, array(), $code);
    }

}