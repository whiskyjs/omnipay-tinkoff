<?php

namespace whiskyjs\Omnipay\Tinkoff\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Http\ClientInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Omnipay\Common\Message\NotificationInterface;

/**
 * Purchase Notification
 */
class PurchaseNotification extends Request implements NotificationInterface
{
    const SIGNATURE_KEYS_TO_SKIP = [
        "Token",
    ];

    const RESPONSE_STATUS_OK = 'OK';
    const RESPONSE_STATUS_INVALID = 'INVALID';

    const NOTIFICATION_STATUS_AUTHORIZED = "AUTHORIZED";
    const NOTIFICATION_STATUS_CONFIRMED = "CONFIRMED";
    const NOTIFICATION_STATUS_REVERSED = "REVERSED";
    const NOTIFICATION_STATUS_REFUNDED = "REFUNDED";
    const NOTIFICATION_STATUS_PARTIAL_REFUNDED = "PARTIAL_REFUNDED";
    const NOTIFICATION_STATUS_REJECTED = "REJECTED";

    /**
     * Incoming notification data.
     *
     * @var array|null
     */
    protected $notificationData;

    /**
     * Contains one of the response statuses above if the notification has been already processed.
     *
     * @var string
     */
    protected $status;

    /**
     * @param ClientInterface $httpClient
     * @param HttpRequest $httpRequest
     */
    public function __construct(ClientInterface $httpClient, HttpRequest $httpRequest)
    {
        parent::__construct($httpClient, $httpRequest);

        try {
            $this->notificationData = $this->decodeBody($httpRequest->getContent());
        } catch (InvalidRequestException $e) {
            // Do nothing for now.
        }
    }

    /**
     * @param string $content
     * @return mixed
     * @throws InvalidRequestException
     */
    protected function decodeBody($content)
    {
        $data = json_decode($content, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new InvalidRequestException(sprintf("JSON parsing error: %s.", json_last_error_msg()));
        } elseif (!$data) {
            throw new InvalidRequestException("The JSON payload is invalid or malformed.");
        }

        return $data;
    }

    /**
     * Nothing is ought to be, so no data is provided.
     *
     * @return array|mixed
     */
    public function getData()
    {
        return [];
    }

    /**
     * Nothing is ought to be sent, so the instance sends nothing.
     *
     * @param mixed $data
     * @return $this
     */
    public function sendData($data)
    {
        return $this;
    }

    /**
     * Process the notification data and automatically accept is valid.
     *
     * @throws InvalidResponseException
     */
    public function process()
    {
        if (!$this->isValid()) {
            $this->invalid();
        } else {
            // Notification of any status must be accepted by the server, otherwise it will be sent again.

            $this->accept();
        }
    }

    /**
     * @return mixed
     */
    public function isSuccess()
    {
        return $this->getNotificationData("Success");
    }

    /**
     * @param string $status
     * @return bool
     */
    public function hasStatus($status)
    {
        return $this->getNotificationData("Status") === $status;
    }

    /**
     * Notify Tinkoff you received the payment details and wish to confirm the payment.
     *
     * @throws InvalidResponseException
     */
    public function accept()
    {
        if (!$this->isValid()) {
            throw new InvalidResponseException('Cannot confirm an invalid notification.');
        }

        $this->sendResponse(static::RESPONSE_STATUS_OK);
    }

    /**
     * Notify Tinkoff you received *something* but the details were invalid and no payment
     * cannot be completed. Invalid should be called if you are not happy with the contents
     * of the POST, such as the MD5 hash signatures did not match or you do not wish to proceed
     * with the order.
     */
    public function invalid()
    {
        $this->sendResponse(static::RESPONSE_STATUS_INVALID);
    }

    /**
     * Respond to Tinkoff confirming or rejecting the notification.
     *
     * @param string The status to send to Tinkoff
     */
    public function sendResponse($status)
    {
        echo $status;

        $this->status = $status;
    }

    /**
     * Overrides the Form/Server/Direct method since there is no
     * getRequest() to inspect in a notification.
     */
    public function getTransactionId()
    {
        return $this->getNotificationData('PaymentId');
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return is_array($this->notificationData) && $this->isValidSignature();
    }

    public function isValidSignature()
    {
        return $this->getSignature($this->notificationData) === $this->getNotificationData("Token");
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->getParameter("orderId");
    }

    /**
     * Nothing is being sent.
     *
     * @return string|null
     */
    protected function getMethod()
    {
        return null;
    }

    /**
     * No data is being sent when processing server-to-server notification.
     *
     * @return array
     */
    protected function getUnsignedData()
    {
        return [];
    }

    /**
     * No response is needed in notifications.
     *
     * @return string|null
     */
    protected function getResponseClass()
    {
        return null;
    }

    /**
     * Was the transaction successful?
     *
     * @return string Transaction status, one of {@link NotificationInterface::STATUS_COMPLETED},
     * {@link NotificationInterface::STATUS_PENDING}, or {@link NotificationInterface::STATUS_FAILED}.
     */
    public function getTransactionStatus()
    {
        if (!$this->isValid() || !$this->isSuccess()) {
            return NotificationInterface::STATUS_FAILED;
        } elseif ($this->hasStatus(self::NOTIFICATION_STATUS_AUTHORIZED)) {
            return NotificationInterface::STATUS_PENDING;
        } else {
            return NotificationInterface::STATUS_COMPLETED;
        }
    }

    /**
     * @return string
     */
    public function getTransactionReference()
    {
        return $this->getNotificationData("PaymentId");
    }

    /**
     * There are no messages in notifications.
     *
     * @return string A response message from the payment gateway
     */
    public function getMessage()
    {
        return "";
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function getNotificationData($key)
    {
        if (isset($this->notificationData[$key])) {
            return $this->notificationData[$key];
        }
    }
}
