<?php

namespace App\Service;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ .'/Generated/PredictionsResponse.php';

use Generated\TeamsRequest;
use Grpc\ChannelCredentials;
use Generated\PredictionsResponse;


class PredictionServiceClient extends \Grpc\BaseStub
{
    public function __construct($hostname)
    {
        parent::__construct($hostname, [
            'credentials' => ChannelCredentials::createInsecure(),
        ]);
    }

    public function GetChampionshipPredictions(TeamsRequest $request)
    {
        return $this->_simpleRequest(
            '/ai_service.PredictionService/GetChampionshipPredictions',
            $request,
            ['\Generated\PredictionsResponse', 'decode']
        );
    }
}