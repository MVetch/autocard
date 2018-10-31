<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 01.10.2018
 * Time: 20:39
 */

namespace AppBundle\Service;


use Symfony\Component\HttpKernel\Exception\HttpException;

class UnknownSoapUserTypeException extends HttpException
{

    public function __construct($message = null, \Exception $previous = null, $code = 0)
    {
        parent::__construct(200, $message, $previous, array(), $code);
    }

}