<?php

namespace App\Services\Communications;

use App\Models\TwilioSms;
use Exception;
use Twilio\Rest\Client;

/**
 * Class TwilioService
 *
 * @package App\Services\Communications
 */
class TwilioService
{
    /**
     * Twilio Client
     */
    protected $client;

    /**
     * Twilio instance parameters
     */
    protected $sid;
    protected $token;
    protected $from_number;

    /**
     * Status Callback Url
     */
    protected $status_callback_url;

    /**
     *
     * @throws \Twilio\Exceptions\ConfigurationException
     */
    public function __construct()
    {

        $this->sid = config('app.twilio.sid');
        $this->token = config('app.twilio.auth_token');
        $this->from_number = config('app.twilio.from_number');

        $this->client = new Client($this->sid,$this->token);

    }

    public function sendMessage($to, $body) : array
    {
        $result = ['success' => false, 'data' => [], 'message' => '', 'twilio_sms_id' => null];

        try{

            $options = array();
            $options['body'] = $body;
            $options['from'] = $this->from_number;
            $options['statusCallback'] = $this->status_callback_url;

            $apiResponse = $this->client->messages->create($to, $options);

            $result['data'] = $apiResponse->toArray();

            if(!empty($result['data']['errorCode'])) {
                throw new Exception('Send sms request failed');
            }
            $result['success'] = true;

            $createdSms = TwilioSms::create([
                'sid' => $result['data']['sid'],
                'direction' => 'sent',
                'from' => $result['data']['from'],
                'to' => $result['data']['to'],
                'status' => $result['data']['status'],
                'body' => $result['data']['body'],
            ]);

            $result['twilio_sms_id'] = $createdSms->id ?? null;


        }catch(Exception $ex){
            $result['success'] = false;
            $result['message'] = $ex->getMessage();
            $result['data']['error_message'] = $result['message'];
        }
        return $result;
    }

    /**
     * Get Twilio Client
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

}
