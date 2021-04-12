<?php

namespace common\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use common\traits\BaseAction;
use backend\modules\common\models\ConfigPageManage;

/**
 * Class Service
 * @package common\components
 * @author Womtech  email:chareler@163.com
 */
class Service extends Component
{
    use BaseAction;
//    protected $pageSize;//每页多少条
    protected $sidx = 'id';//排序的字段
    protected $sord = 'desc';//正序或倒序

    /**
     * 子服务
     *
     * @var
     */
    public $childService;

    /**
     * 已实例化的子服务
     *
     * @var
     */
    protected $_childService;

    /**
     * 获取 services 里面配置的子服务 childService 的实例
     *
     * @param $childServiceName
     * @return mixed
     * @throws InvalidConfigException
     */
    protected function getChildService($childServiceName)
    {
        if (!isset($this->_childService[$childServiceName])) {
            $childService = $this->childService;

            if (isset($childService[$childServiceName])) {
                $service = $childService[$childServiceName];
                $this->_childService[$childServiceName] = Yii::createObject($service);
            } else {
                throw new InvalidConfigException('Child Service [' . $childServiceName . '] is not find in ' . get_called_class() . ', you must config it! ');
            }
        }

        return $this->_childService[$childServiceName] ?? null;
    }

    /**
     * @param string $attr
     * @return mixed
     * @throws InvalidConfigException
     */
    public function __get($attr)
    {
        return $this->getChildService($attr);
    }
}