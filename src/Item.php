<?php

namespace whiskyjs\Omnipay\Tinkoff;

use Omnipay\Common\Item as OmniItem;

class Item extends OmniItem implements \JsonSerializable
{
    /**
     * @return string
     */
    public function getPaymentMethod()
    {
        return $this->getParameter('paymentMethod');
    }

    /**
     * @param string $value
     * @return $this Item
     */
    public function setPaymentMethod($value)
    {
        return $this->setParameter('paymentMethod', $value);
    }

    /**
     * @return string
     */
    public function getPaymentObject()
    {
        return $this->getParameter('paymentMethod');
    }

    /**
     * @param string $value
     * @return $this Item
     */
    public function setPaymentObject($value)
    {
        return $this->setParameter('paymentObject', $value);
    }

    /**
     * @return string
     */
    public function getTax()
    {
        return $this->getParameter('tax');
    }

    /**
     * @param string $value
     * @return $this Item
     */
    public function setTax($value)
    {
        return $this->setParameter('tax', $value);
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $result = [];

        foreach ($this->getParameters() as $key => $value) {
            if ($value) {
                $result[ucfirst($key)] = $value;
            }
        }

        $result["Price"] = intval($result["Price"]) * 100;
        $result["Amount"] = $result["Price"] * intval($result["Quantity"]);

        return $result;
    }
}
