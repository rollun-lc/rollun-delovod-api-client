<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 07.06.17
 * Time: 11:09
 */

namespace rollun\delovod\Api;

use rollun\delovod\ApiException;
use rollun\installer\Command;
use rollun\utils\Json\Serializer;
use Zend\Http\Client;

class DelovodApi implements DelovodApiInterface
{
    const URL = "https://delovod.ua/api/";

    const VERSION = "0.1";

    protected $key;

    /**
     * RequestObjectApi constructor.
     * @param string $key
     */
    public function __construct($key)
    {
        $this->key = $key;
    }

    /**
     * Save object.
     * @param array $params
     * @return bool
     */
    public function saveObject(array $params)
    {
        $client = $this->initClient("saveObject", $params);
        $data = $this->sendRequest($client);

        $this->checkResponseData($data);
        return $data == "ok";
    }

    /**
     * Init Zend Client for send request to api.
     * @param $action
     * @param array $params
     * @param array $options
     * @return Client
     */
    protected function initClient($action, array $params, array $options = [])
    {
        $packet = [
            'version' => static::VERSION,
            'key' => $this->key,
            'action' => $action,
            'params' => $params
        ];
        $client = new Client(static::URL);
        $client->setMethod("POST");
        $client->setHeaders(['Content-Type' => 'application/x-www-form-urlencoded']);
        $rawBody = "packet=" . Serializer::jsonSerialize($packet);

        $file = Command::getDataDir() . "delovod_req_logs/". uniqid($action."_req_", true);
        file_put_contents($file, $rawBody);

        $client->setRawBody($rawBody);
        return $client;
    }

    /**
     * Send request and unserialize json response data if response is Ok. Else throw exception.
     * @param Client $client
     * @return mixed
     * @throws ApiException
     */
    protected function sendRequest(Client $client)
    {
        $response = $client->send();
        if ($response->isOk()) {
            $data = Serializer::jsonUnserialize($response->getBody());
            return $data;
        }
        throw new ApiException("Response error. Status: " . $response->getStatusCode());
    }

    /**
     * Check response data, if has error throw exception
     * @param $data
     * @throws ApiException
     */
    protected function checkResponseData($data)
    {
        if (is_array($data) && isset($data['error'])) {
            $message = $data['error'];

            if(isset($data['clientMessages'])) {
                $message .= " ClientMessages: ";
                foreach ($data['clientMessages'] as $clientMessage) {
                    $message .= $clientMessage['data'] . " ";
                }
            }
            throw new ApiException("Api error. Message: $message");
        }
    }

    /**
     * Request objects by filter from entity which set in $from
     * @param array $params
     * @return \array[]|mixed
     */
    public function requestObjects(array $params = [])
    {
        $client = $this->initClient("request", $params);
        $data = $this->sendRequest($client);
        $this->checkResponseData($data);
        return $data;
    }

    /**
     * Get object by id.
     * @param $id
     * @return array
     */
    public function getObject($id)
    {
        $client = $this->initClient("getObject", ['id' => $id]);
        $data = $this->sendRequest($client);
        $this->checkResponseData($data);
        return $data;
    }

    /**
     * @waring RED DOC https://delovod.ua/help/ru/API_001_setDelMark !!!
     * Get object by id.
     * @param $id
     * @return array
     */
    public function setDelMark($id)
    {
        $client = $this->initClient("setDelMark", ['header' => ['id' => $id]]);
        $data = $this->sendRequest($client);
        $this->checkResponseData($data);
        return $data;
    }
}
