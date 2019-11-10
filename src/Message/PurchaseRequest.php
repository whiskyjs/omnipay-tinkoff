<?php

namespace whiskyjs\Omnipay\Tinkoff\Message;

use DateTime;
use whiskyjs\Omnipay\Tinkoff\Common\RequestHelpers;
use whiskyjs\Omnipay\Tinkoff\Receipt;

/**
 * Purchase Request
 *
 */
class PurchaseRequest extends Request
{
    use RequestHelpers;

    const SIGNATURE_KEYS_TO_SKIP = [
        "DATA",
        "Receipt",
    ];

    /**
     * @return string
     */
    protected function getMethod()
    {
        return "Init";
    }

    /**
     * @return int
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    protected function getAmountInt()
    {
        return ceil($this->getAmount() * 100);
    }

    /**
     * @return string
     */
    public function getTransactionId()
    {
        return $this->getOrderId();
    }

    /**
     * @param $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setTransactionId($value)
    {
        return $this->setOrderId($value);
    }

    /**
     * @return string
     */
    public function getOrderId()
    {
        return $this->getParameter("orderId");
    }

    /**
     * @param $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setOrderId($value)
    {
        return $this->setParameter("orderId", $value);
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->getParameter("language");
    }

    /**
     * @param string $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setLanguage($value)
    {
        return $this->setParameter("language", $value);
    }

    /**
     * @return string
     */
    public function getCustomerKey()
    {
        return $this->getParameter("customerKey");
    }

    /**
     * @param string $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setCustomerKey($value)
    {
        return $this->setParameter("customerKey", $value);
    }

    /**
     * @return bool
     */
    public function getRecurrent()
    {
        return $this->getParameter("recurrent");
    }

    /**
     * @param bool $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setRecurrent($value)
    {
        return $this->setParameter("recurrent", $value);
    }

    /**
     * @return string
     */
    public function getRecurrentString()
    {
        return $this->getRecurrent() ? "Y" : "N";
    }

    /**
     * @return DateTime|int|string
     */
    public function getRedirectDueDate()
    {
        return $this->getParameter("redirectDueDate");
    }

    /**
     * @param DateTime|int|string $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setRedirectDueDate($value)
    {
        return $this->setParameter("redirectDueDate", $value);
    }

    /**
     * @return DateTime|null
     * @throws \Exception
     */
    public function getRedirectDueDateObject()
    {
        $date = $this->getRedirectDueDate();

        if (is_numeric($date)) {
            return (new DateTime())->setTimestamp(intval($date));
        } elseif (is_string($date)) {
            return (new DateTime())->setTimestamp(strtotime($date));
        } elseif ($date instanceof DateTime) {
            return $date;
        }

        return null;
    }

    /**
     * @return array
     */
    public function getExtraData()
    {
        return $this->getParameter("extraData");
    }

    /**
     * @param array $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setExtraData($value)
    {
        return $this->setParameter("extraData", $value);
    }

    /**
     * @return array
     */
    protected function getExtraDataArray()
    {
        $data = $this->getExtraData();

        if (is_object($data)) {
            return (array) $data;
        } elseif (is_array($data)) {
            return $data;
        } elseif (is_scalar($data)) {
            return [
                "value" => $data,
            ];
        }

        return null;
    }

    /**
     * @return string
     */
    public function getNotificationUrl()
    {
        return $this->getParameter("notificationUrl");
    }

    /**
     * @param string $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setNotificationUrl($value)
    {
        return $this->setParameter("notificationUrl", $value);
    }

    /**
     * @return string
     */
    public function getSuccessUrl()
    {
        return $this->getParameter("successUrl");
    }

    /**
     * @param string $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setSuccessUrl($value)
    {
        return $this->setParameter("successUrl", $value);
    }

    /**
     * @return string
     */
    public function getFailUrl()
    {
        return $this->getParameter("failUrl");
    }

    /**
     * @param string $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setFailUrl($value)
    {
        return $this->setParameter("failUrl", $value);
    }

    /**
     * @return bool
     */
    public function getTwoStagePayment()
    {
        return $this->getParameter("twoStagePayment");
    }

    /**
     * @param bool $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setTwoStagePayment($value)
    {
        return $this->setParameter("twoStagePayment", $value);
    }

    /**
     * @return string
     */
    public function getTwoStagePaymentString()
    {
        return $this->getTwoStagePayment() ? "T" : "O";
    }

    /**
     * @return Receipt
     */
    public function getReceipt()
    {
        return $this->getParameter("receipt");
    }

    /**
     * @param Receipt
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setReceipt($value)
    {
        return $this->setParameter("receipt", $value);
    }

    /**
     * @return array
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     * @throws \Exception
     */
    protected function getUnsignedData()
    {
        $data = [
            "TerminalKey" => $this->getTerminalId(),
            "Amount" => $this->getAmountInt(),
            "OrderId" => $this->getTransactionId(),
            "IP" => $this->httpRequest->server->get("SERVER_ADDR"),
            "PayType" => $this->getTwoStagePaymentString(),
        ];

        $this->setIfExistsArray([
            "Description" => $this->getDescription(),
            "Language" => $this->getLanguage(),
            "CustomerKey" => $this->getCustomerKey(),
            "Recurrent" => $this->getRecurrent(),
            "RedirectDueDate" => $this->getRedirectDueDateObject(),
            "DATA" => $this->getExtraDataArray(),
            "NotificationURL" => $this->getNotificationUrl(),
            "SuccessURL" => $this->getSuccessUrl(),
            "FailURL" => $this->getFailUrl(),
            "Receipt" => $this->getReceipt(),
        ], $data);

        return $data;
    }

    /**
     * @return string
     */
    protected function getResponseClass()
    {
        return "whiskyjs\Omnipay\Tinkoff\Message\PurchaseResponse";
    }
}
