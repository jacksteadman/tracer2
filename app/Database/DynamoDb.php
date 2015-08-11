<?php
namespace App\Database;

use App\Contracts\Action;
use \Aws\Sdk;
use \Aws\DynamoDb\Marshaler;
use \Aws\DynamoDb\Exception\DynamoDbException;

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

    public function getActionByUniquenessKey($user_id, $uniqueness_key) {
        $response = $this->client->query([
            'TableName' => 'tr_actions',
            'IndexName' => 'uniqueness_key-index',
            'KeyConditionExpression' => 'user_id = :user_id AND uniqueness_key = :u_key',
            'ExpressionAttributeValues' => [
                ':user_id' => [ 'S' => $user_id ],
                ':u_key' => [ 'S' => $uniqueness_key ],
            ],
            'Limit' => 1,
        ]);

        if (!empty($response['Items'])) {
            $action = new \App\Action();
            $action->fromArray($this->marshaler->unmarshalItem($response['Items'][0]));
            return $action;
        }

        return null;
    }

    public function getCounter($key) {
        $response = $this->client->GetItem([
            'TableName' => 'tr_counters',
            'Key' => [
                'counter_key' => [
                    'S' => $key,
                ]
            ]
        ]);

        if (isset($response['Item'])) {
            $counter = $this->marshaler->unmarshalItem($response['Item']);
            return $counter['v'];
        }

        return null;
    }

    public function storeAction(\App\Action $action) {
        $data = $action->toArray();
        // TODO error handling
        $this->client->PutItem([
            'TableName' => 'tr_actions',
            'Item' => $this->marshaler->marshalItem($data),
        ]);
    }

    public function incrementCounter($key) {
        $this->createCounterIfNotExists($key);
        $rv = $this->client->UpdateItem(
            [
                'TableName' => 'tr_counters',
                'Key' => [ 'counter_key' => [ 'S' => $key ]],
                'UpdateExpression' => 'SET v = v + :inc',
                'ExpressionAttributeValues' => [ ':inc' => [ 'N' => 1 ]],
                'ReturnValues' => 'UPDATED_NEW',
            ]
        );

        return $rv['Attributes']['v']['N'];
    }

    public function createCounterIfNotExists($key) {
        try {
            $this->client->UpdateItem(
                [
                    'TableName' => 'tr_counters',
                    'Key' => ['counter_key' => ['S' => $key]],
                    'UpdateExpression' => 'SET v = :val',
                    'ExpressionAttributeValues' => [':val' => ['N' => 0]],
                    'ConditionExpression' => 'attribute_not_exists (v)',
                ]
            );
        } catch (DynamoDbException $e) {
            if ($e->getAwsErrorCode() === 'ConditionalCheckFailedException') {}
            // TODO
        }
    }

    public function storeReferralCode($code, $user_id, $timestamp)
    {
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
