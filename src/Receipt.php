<?php

namespace whiskyjs\Omnipay\Tinkoff;

use JsonSerializable;
use Omnipay\Common\ParametersTrait;

class Receipt implements JsonSerializable
{
    use ParametersTrait;

    /**
     * @param array $params
     */
    public function __construct($params = [])
    {
        $this->initialize($params);
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $result = [];

        foreach ($this->getParameters() as $key => $value) {
            if ($value) {
                if (is_object($value) && ($value instanceof JsonSerializable)) {
                    $result[ucfirst($key)] = $value->jsonSerialize();
                } else {
                    $result[ucfirst($key)] = $value;
                }
            }
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->getParameter("email");
    }

    /**
     * @param $value
     * @return static
     */
    public function setEmail($value)
    {
        return $this->setParameter("email", $value);
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->getParameter("phone");
    }

    /**
     * @param $value
     * @return static
     */
    public function setPhone($value)
    {
        return $this->setParameter("phone", $value);
    }

    /**
     * @return string
     */
    public function getEmailCompany()
    {
        return $this->getParameter("emailCompany");
    }

    /**
     * @param $value
     * @return static
     */
    public function setEmailCompany($value)
    {
        return $this->setParameter("emailCompany", $value);
    }

    /**
     * @return string
     */
    public function getTaxation()
    {
        return $this->getParameter("taxation");
    }

    /**
     * @param $value
     * @return static
     */
    public function setTaxation($value)
    {
        return $this->setParameter("taxation", $value);
    }

    /**
     * @return string
     */
    public function getItems()
    {
        return $this->getParameter("items");
    }

    /**
     * @param $value
     * @return static
     */
    public function setItems($value)
    {
        return $this->setParameter("items", $value);
    }
}
