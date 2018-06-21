<?php

namespace OmerKocaoglu\SMSService\Services;

use OmerKocaoglu\SMSService\Constants\TwilioSMSStatus;
use OmerKocaoglu\SMSService\Error\ErrorDetailModel;
use OmerKocaoglu\SMSService\Error\HttpStatusCode;
use OmerKocaoglu\SMSService\ServiceBase;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Api\V2010\Account\MessageInstance;
use Twilio\Rest\Client;

class TwilioService extends ServiceBase
{
    /** @var Client */
    private $twilio = null;
    /** @var bool */
    private $is_phone_number_validated = false;

    /** @var string */
    private $valid_phone_pattern = "/\+?[0-9]+$/";

    public function __construct($account_ids, $auth_token)
    {
        if ($this->twilio === null) {
            $this->twilio = new Client($account_ids, $auth_token);
        }
    }

    /**
     * @param string $phone_number
     * @return bool
     */
    public function isPhoneNumberValid($phone_number)
    {
        if (preg_match($this->valid_phone_pattern, $phone_number) === 1) {
            $this->is_phone_number_validated = true;
            return true;
        }

        return false;
    }

    /**
     * @param string $from_number
     * @param string $to_number
     * @param string $body
     * @return ErrorDetailModel|MessageInstance[]
     */
    public function sendMessage($from_number, $to_number, $body)
    {
        if ($this->is_phone_number_validated === false) {
            if ($this->isPhoneNumberValid($to_number) === false) {
                return $this->createErrorDetail(
                    HttpStatusCode::UNPROCESSABLE_ENTITY,
                    sprintf(
                        'Twilio SMS Service phone number cannot validated %s',
                        $to_number
                    )
                );
            }
        }

        try {
            /** @var MessageInstance $twilio_response */
            $twilio_response = $this->twilio->messages->create($to_number, ['from' => $from_number, 'body' => $body]);
            if ($twilio_response->status === TwilioSMSStatus::FAILED || $twilio_response->status === TwilioSMSStatus::UNDELIVERED) {
                return $this->createErrorDetail(
                    HttpStatusCode::INTERNAL_SERVER_ERROR,
                    sprintf(
                        'SmsService sendMessage twilio failed with status %s',
                        $twilio_response->status
                    )
                );
            }
            return $twilio_response->toArray();
        } catch (TwilioException $exception) {
            return $this->createErrorDetail(
                HttpStatusCode::INTERNAL_SERVER_ERROR,
                $exception->getMessage()
            );
        }
    }
}
