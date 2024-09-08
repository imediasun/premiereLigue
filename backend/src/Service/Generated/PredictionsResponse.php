<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: ai_service.proto

namespace Generated;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Answer with predictions
 *
 * Generated from protobuf message <code>ai_service.PredictionsResponse</code>
 */
class PredictionsResponse extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>repeated .ai_service.Prediction predictions = 1;</code>
     */
    private $predictions;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type array<\Generated\Prediction>|\Google\Protobuf\Internal\RepeatedField $predictions
     * }
     */
    public function __construct($data = NULL) {
        \GPBMetadata\AiService::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>repeated .ai_service.Prediction predictions = 1;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getPredictions()
    {
        return $this->predictions;
    }

    /**
     * Generated from protobuf field <code>repeated .ai_service.Prediction predictions = 1;</code>
     * @param array<\Generated\Prediction>|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setPredictions($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::MESSAGE, \Generated\Prediction::class);
        $this->predictions = $arr;

        return $this;
    }

}

