<?php
namespace App\Database;

use App\Contracts\Action;
use \Aws\Sdk;
use \Aws\DynamoDb\Marshaler;

class DynamoDb implements Action\Store {

    /**
     * @var \Aws\DynamoDb\DynamoDbClient
     */
    private $client;

    /**
     * @var \Aws\DynamoDb\Marshaler
     */
    private $marshaler;

    function __construct(Sdk $sdk) {
        $this->client = $sdk->createClient('DynamoDb');
        $this->marshaler = new Marshaler();
    }

    public function storeAction(\App\Action $action) {
        $data = $action->toArray();
        // TODO error handling
        $this->client->PutItem([
            'TableName' => 'tr_actions',
            'Item' => $this->marshaler->marshalItem($data),
        ]);
    }

    public function storeReferralCode($code, $user_id, $timestamp) {
        $data = [
            'code' => $code,
            'user_id' => $user_id,
            'timestamp' => $timestamp,
        ];

        // TODO error handling

        $response = $this->client->GetItem([
            'TableName' => 'tr_referralcodes',
            'Key' => [
                'code' => [
                    'S' => $code,
                ]
            ]
        ]);

        if (!empty($response['Item'])) {
            throw new \Exception('Code already exists');
        }

        $this->client->PutItem([
            'TableName' => 'tr_referralcodes',
            'Item' => $this->marshaler->marshalItem($data),
        ]);
    }



}
