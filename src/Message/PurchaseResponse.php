<?php

namespace whiskyjs\Omnipay\Tinkoff\Message;

use Omnipay\Common\Message\RedirectResponseInterface;

class PurchaseResponse extends Response implements RedirectResponseInterface
{
    /**
     * Request is never successful.
     *
     * @return bool
     */
    public function isSuccessful()
    {
        return false;
    }

    /**
     * Redirect is always required.
     *
     * @return bool
     */
    public function isRedirect()
    {
        return true;
    }

    /**
     * @return string|null
     */
    public function getRedirectUrl()
    {
        return $this->data["PaymentURL"];
    }

    /**
     * @return string
     */
    public function getRedirectMethod()
    {
        return 'GET';
    }
}
