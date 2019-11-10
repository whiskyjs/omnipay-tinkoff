<?php

namespace whiskyjs\Omnipay\Tinkoff\Message;

use Omnipay\Common\Message\AbstractRequest;

/**
 * Request
 *
 */
abstract class Request extends AbstractRequest
{
    const SIGNATURE_KEYS_TO_SKIP = [];

    protected $liveEndpoint = 'https://securepay.tinkoff.ru/v2/';
    protected $testEndpoint = 'https://securepay.tinkoff.ru/v2/';

    /**
     * @return string
     */
    public function getTerminalId()
    {
        return $this->getParameter('terminalId');
    }

    /**
     * @param string $value
     * @return AbstractRequest
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
     * @return AbstractRequest
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
     * @return AbstractRequest
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
     * @return AbstractRequest
     */
    public function setTestPassword($value)
    {
        return $this->setParameter('testPassword', $value);
    }

    protected function getBaseData()
    {
        return [
            'transaction_id' => $this->getTransactionId(),
            'expire_date' => $this->getCard()->getExpiryDate('mY'),
            'start_date' => $this->getCard()->getStartDate('mY'),
        ];
    }

    /**
     * @return string
     */
    protected function getEndpoint()
    {
        return $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
    }

    /**
     * @param $data
     * @return Response|null
     */
    protected function createResponse($data)
    {
        $responseClass = $this->getResponseClass();

        if (!$responseClass) {
            return null;
        }

        return $this->response = new $responseClass($this, $data);
    }

    /**
     * @return array
     */
    public function getData()
    {
        $unsignedData = $this->getUnsignedData();

        $signedData = array_merge($unsignedData, [
            "Token" => $this->getSignature($unsignedData),
        ]);

        return $signedData;
    }

    /**
     * @param array $data
     * @return string
     */
    protected function getSignature($data)
    {
        $data["Password"] = $this->getTestMode() ? $this->getTestPassword() : $this->getPassword();

        ksort($data);

        $tokenStr = '';

        foreach ($data as $key => $value) {
            if (in_array($key, static::SIGNATURE_KEYS_TO_SKIP)) {
                continue;
            }

            $tokenStr .= is_bool($value) ? ($value ? "true" : "false") : $value;
        }

        return hash("sha256", $tokenStr);
    }

    /**
     * @param array $inputData
     * @return Response
     */
    public function sendData($inputData)
    {
        $path = $this->getEndpoint();

        if ($this->getMethod()) {
            $path .= $this->getMethod();
        }

        $response = $this->httpClient->request("POST", $path, [
            "Content-Type" => "application/json",
        ], json_encode($inputData));

        $outputData = json_decode($response->getBody()->getContents(), true);

        return $this->createResponse($outputData);
    }

    /**
     * @return string|null
     */
    abstract protected function getMethod();

    /**
     * @return array|null
     */
    abstract protected function getUnsignedData();

    /**
     * @return string|null
     */
    abstract protected function getResponseClass();
}
