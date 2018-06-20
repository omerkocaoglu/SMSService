<?php

namespace OmerKocaoglu\SMSService\Clients;

use Twilio\Rest\Client;

class TwilioClient
{
    public function __construct($account_ids, $auth_token)
    {
        return new Client($account_ids, $auth_token);
    }
}
