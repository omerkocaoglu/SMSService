<?php

namespace OmerKocaoglu\SMSService\Error;

interface ErrorDetailInterface
{
    /**
     * @param int $status_code
     * @param string $message
     * @return ErrorDetailModel
     */
    public function createErrorDetail($status_code, $message);
}
