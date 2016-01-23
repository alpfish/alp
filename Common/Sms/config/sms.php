<?php
return[

    /* *
     * 短信发件人（网站名称）
     * */
    'product' => '【LerHoo网】',

    /* *
     * 短信服务提供商 必须添加IP白名单
     * */
    'provider' => [
        'ali' => [
            'appKey' => '23290957',
            'secretKey' => 'ebe2b6947169fdbf51d823a185571bb9',
        ],
    ],

    /* *
     * 模板集合
     *
     * 模板名代码(code)和签名(signName)都是供应商审核通过后才能使用
     * */
    'templates' => [

        // 默认模板(必须有)
        'default' => [
            'code' => 'SMS_3915391',
            'signName' => '身份验证',
        ],

        // 身份验证
        'authentication' => [
            'code' => 'SMS_3915391',
            'signName' => '身份验证',
        ],

        // 注册
        'register' => [
            'code' => 'SMS_3915388',
            'signName' => '注册验证',
        ],
    ],

    /* *
     * SMS 短信节流阀
     * */
    'limit' => [

        /* *
         * 客户限制
         * 发送成功再计数
         * (设置合理的地区、次数、时间，以不影响体验为主，防攻击为辅)
         * */

        'cLmtSmsLen' => 30,      // 客户端超限后限制使用SMS功能时长（单位：分钟）， 即缓存重置时间

        'mArea' => '四川',    // 可用手机地区

        'mAreaLvl' => '省',   // 地区等级

        'mSendSmsTimes' => 2,    // 每手机时间内可发送SMS次数

        'mValidSmsTimes' => 2,   // 每手机时间内可验证SMS次数

        'ipArea' => '绵阳',    // 可用IP地区

        'ipAreaLvl' => '市',   // 地区等级

        'ipSendSmsTimes' => 5,    // 每个IP可发送SMS次数

        'ipValidSmsTimes' => 8,   // 每个IP可验证SMS次数


        /* *
         * 网站限制
         * 发送成功才计数
         * 设置合理的总量及时间，以预防恶意攻击为主，不影响体验为辅
         * */

        'webLmtSmsTot' => 30,     // 时间内限制总量

        'webLmtSmsTotLen' => 5,   // 规定限制时长(单位：分钟)， 即缓存重置时间
    ],

    /* *
     * 发生异常是否发送邮箱通知站长
     * */
    'isSendErrEmail' => true,

];

