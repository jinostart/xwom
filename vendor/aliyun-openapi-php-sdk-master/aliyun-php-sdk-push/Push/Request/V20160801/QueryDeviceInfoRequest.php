<?php

namespace Push\Request\V20160801;

/**
 * @deprecated Please use https://github.com/aliyun/openapi-sdk-php
 *
 * Request of QueryDeviceInfo
 *
 * @method string getDeviceId()
 * @method string getAppKey()
 */
class QueryDeviceInfoRequest extends \RpcAcsRequest
{

    /**
     * @var string
     */
    protected $method = 'POST';

    /**
     * Class constructor.
     */
    public function __construct()
    {
        parent::__construct(
            'Push',
            '2016-08-01',
            'QueryDeviceInfo'
        );
    }

    /**
     * @param string $deviceId
     *
     * @return $this
     */
    public function setDeviceId($deviceId)
    {
        $this->requestParameters['DeviceId'] = $deviceId;
        $this->queryParameters['DeviceId'] = $deviceId;

        return $this;
    }

    /**
     * @param string $appKey
     *
     * @return $this
     */
    public function setAppKey($appKey)
    {
        $this->requestParameters['AppKey'] = $appKey;
        $this->queryParameters['AppKey'] = $appKey;

        return $this;
    }
}
