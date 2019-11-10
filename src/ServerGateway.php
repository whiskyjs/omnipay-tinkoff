<?php

namespace whiskyjs\Omnipay\Tinkoff;

use whiskyjs\Omnipay\Tinkoff\Message\Notification;
use whiskyjs\Omnipay\Tinkoff\Message\PurchaseNotification;
use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Message\NotificationInterface;

/**
 * Tinkoff Gateway
 * @method \Omnipay\Common\Message\RequestInterface authorize(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface completeAuthorize(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface capture(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface completePurchase(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface refund(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface void(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface createCard(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface updateCard(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface deleteCard(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface fetchTransaction(array $options = [])
 * @method \Omnipay\Common\Message\RequestInterface purchase(array $options = array())
 */
class ServerGateway extends AbstractGateway
{
    public function getName()
    {
        return 'Tinkoff';
    }

    public function getDefaultParameters()
    {
        return [
            'terminalId' => '',
            'password' => '',

            'testTerminalId' => '',
            'testPassword' => '',

            'testMode' => false,
        ];
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters->all();
    }

    /**
     * @return string
     */
    public function getTerminalId()
    {
        return $this->getParameter('terminalId');
    }

    /**
     * @param string $value
     * @return ServerGateway
     */
    public function setTerminalId($value)
    {
        return $this->setParameter('terminalId', $value);
    }

    /**
     * @return string
     */
    public function getTestTerminalId()
    {
        return $this->getParameter('testTerminalId');
    }

    /**
     * @param string $value
     * @return ServerGateway
     */
    public function setTestTerminalId($value)
    {
        return $this->setParameter('testTerminalId', $value);
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->getParameter('password');
    }

    /**
     * @param string $valus
     * @return ServerGateway
     */
    public function setPassword($value)
    {
        return $this->setParameter('password', $value);
    }

    /**
     * @return string
     */
    public function getTestPassword()
    {
        return $this->getParameter('testPassword');
    }

    /**
     * @param string $value
     * @return ServerGateway
     */
    public function setTestPassword($value)
    {
        return $this->setParameter('testPassword', $value);
    }

    /**
     * Purchase request
     *
     * @param array $options
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function acceptNotification(array $options = [])
    {
        return $this->createRequest(PurchaseNotification::class, $options);
    }
}
