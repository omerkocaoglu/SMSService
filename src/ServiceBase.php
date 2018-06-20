<?php

namespace OmerKocaoglu\SMSService;

use OmerKocaoglu\SMSService\Error\ErrorDetailModel;
use OmerKocaoglu\SMSService\Error\ErrorDetailInterface;

class ServiceBase implements ErrorDetailInterface
{
    /**
     * @param int $status_code
     * @param string $message
     * @return ErrorDetailModel
     */
    public function createErrorDetail($status_code, $message)
    {
        $model = new ErrorDetailModel();
        $model->status_code = $status_code;
        $model->message = $message;
        return $model;
    }
}
