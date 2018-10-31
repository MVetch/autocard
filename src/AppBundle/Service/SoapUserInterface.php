<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 21.02.18
 * Time: 14:16
 */

namespace AppBundle\Service;


interface SoapUserInterface
{

    public function getCredentials();

    public function getType();

}