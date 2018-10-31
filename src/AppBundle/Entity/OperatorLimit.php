<?php
/**
 * Created by PhpStorm.
 * User: t0wqa
 * Date: 04.02.18
 * Time: 10:00
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="operator_limit")
 */
class OperatorLimit
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Operator")
     * @ORM\JoinColumn(name="operator_id", referencedColumnName="id")
     */
    protected $operator;

    /**
     * @ORM\ManyToOne(targetEntity="Limit")
     * @ORM\JoinColumn(name="limit_id", referencedColumnName="id")
     */
    protected $limit;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @param mixed $operator
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;
    }

    /**
     * @return mixed
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param mixed $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }


}