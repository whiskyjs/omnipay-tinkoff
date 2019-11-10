<?php

namespace whiskyjs\Omnipay\Tinkoff\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

/**
 * Response
 */
class Response extends AbstractResponse
{
    public function __construct(RequestInterface $request, $data)
    {
        parent::__construct($request, $data);

        $this->request = $request;
        $this->data = $data;
    }

    public function isSuccessful()
    {
        return $this->data['Success'];
    }

    public function getTransactionId()
    {
        if (isset($this->data['PaymentId'])) {
            return $this->data['PaymentId'];
        }
    }

    /**
     * @return null|string
     */
    public function getMessage()
    {
        if (isset($this->data['Message'])) {
            return $this->data['Message'];
        }

        return null;
    }

    /**
     * @return null|string
     */
    public function getDetailMessage()
    {
        if (isset($this->data['DetailMessage'])) {
            return $this->data['DetailMessage'];
        }

        return null;
    }

    /**
     * @return null|int
     */
    public function getCode()
    {
        if (isset($this->data['Error'])) {
            return $this->data['Error'];
        }

        return null;
    }
}
