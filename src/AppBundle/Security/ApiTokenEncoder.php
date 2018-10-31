<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 31.01.18
 * Time: 4:44
 */

namespace AppBundle\Security;


class ApiTokenEncoder
{

    public function encode($login)
    {
        return trim(uniqid() . '.' . base64_encode($login) . '.' . base64_encode(time()) . '.' . md5(time()));
    }

    public function decode($apiToken)
    {
        //
    }

}