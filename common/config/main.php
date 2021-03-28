<?php
return [
    /** ------ 开发者信息 ------ **/
    'name' => 'xwom系统',//默认站点名称
    'version' => '1.0.1',//开发版本号

    /** ------ 开发 编码、语言、时区信息 ------ **/
    'charset' => 'utf-8',//系统默认编码
    'sourceLanguage' => 'zh-cn',//系统默认语言包
    'language' => 'zh-CN',//系统默认语言
    'timeZone' => 'Asia/Shanghai',//系统默认时区
    
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',//定义vendorPath路径
    /**加载yii2-kartikgii 的datecontrol 模块**/
    'modules' => [
            'datecontrol' =>  [
                'class' => 'kartik\datecontrol\Module',
                // format settings for displaying each date attribute
                'displaySettings' => [
                    'date' => 'd-m-Y',
                    'time' => 'H:i:s A',
                    'datetime' => 'd-m-Y H:i:s A',
                ],
                // format settings for saving each date attribute
                'saveSettings' => [
                    'date' => 'Y-m-d', 
                    'time' => 'H:i:s',
                    'datetime' => 'Y-m-d H:i:s',
                ],
                // automatically use kartik\widgets for each of the above formats
                'autoWidget' => true,
            ]
        ],
    'components' => [
        /** ------ 加载yii2-swiftmailer扩展 ------ **/
        //这里省略，单独在各个app* 的main里配置
        
        /** ------ 缓存 ------ **/
        'cache' => [
            'class' => 'yii\caching\FileCache',
            /**
             * 文件缓存一定要有，不然有可能会导致缓存数据获取失败的情况
             *
             * 注意如果要改成非文件缓存请删除，否则会报错
             */
            'cachePath' => '@backend/runtime/cache'
        ],
        /** ------ 格式化时间配置 ------ **/
       'formatter' => [
            'dateFormat' => 'yyyy-MM-dd',
            'timeFormat' => 'HH:mm:ss',
            'datetimeFormat' => 'yyyy-MM-dd HH:mm:ss',//yyyy-MM-dd HH:mm:ss
            'decimalSeparator' => ',',
            'thousandSeparator' => ' ',
            'currencyCode' => 'CNY',
            'nullDisplay' => '-',
        ],
        /** ------ 服务层 ------ **/
        'services' => [
            'class' => 'services\Application',
        ],
        /** ------ redis配置 ------ **/
        
        /** ------ 网站碎片管理 ------ **/
        
        /** ------ 访问设备信息 ------ **/
        
        /** ------ 队列设置 ------ **/
        
        /** ------ 公用支付 ------ **/
        
        /** ------ 上传组件 ------ **/
        
        /** ------ 二维码 ------ **/
        
        /** ------ 微信SDK ------ **/
        
        /** ------ 国际化语言配置 ------ **/
        
        /** ------ CDN支持 七牛 腾讯云 阿里云 网易云 ------ **/
        
        
        /** ------ 注册阿里云短信SDK ------ **/
        
        
        /** ------ 阿里云OSS ------ **/
        
        
        /** ------ 注册xunsearch全文检索sdk------ **/
        
        
        
        /*
         * 国际化语言配置使用，这里的设置的关键在于两个语言设置，即 sourceLanguage（源语言） 和 language（目标语言） 的设置，
         * 该翻译服务就是将网站从源语言翻译成目标语言的实现，其中目标语言是可以随时更改的    
         */
        
        'i18n' => [
            'translations' => [//多语言包设置
                'workflow' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'sourceLanguage' => 'en-US',
                    'basePath' => '@app/messages',
                    'fileMap' => [
                        'app' => 'workflow.php',
                    ],
                ],
                'app*' => [
                    'class' => yii\i18n\PhpMessageSource::className(),
                    'basePath' => '@backend/messages',//定义目标语言类的路径
                    'sourceLanguage' => 'en-US',//zh-CN en-US
                    'fileMap' => [
                        'app' => 'app.php',
                        'app/error' => 'error.php',
                    ],
                ],

                
            ],
        ],
    ],
];
