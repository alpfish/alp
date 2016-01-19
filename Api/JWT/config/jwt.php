<?php

return [

    // 随机密钥
    'key' => 'ajDFskfdlASDFjzhxiFGvDSmwrnbasASdiofcjdFGHsafknjlsaf',

    // 算法，只支持'HS256', 'HS384', 'HS512' and 'RS256'
    'alg' => 'HS512',

    'touch' => 60*60, // token刷新频率(即请求TOKEN的'iat'超过多少秒后自动刷新并返回刷新后的TOKEN)

    // 配置Body部分的数据数组：权利断言/申明，（生成JWT时叫$claims，客户端传回来验证叫$payload）
    'claims' => [
        'iss' => '',  // 发行人 Issuer 一般为网站名
        'aud' => '',    //  接受人Audience（一般为用户ID，或自定义uid，这里不用设置）
        'iat' => '',  // 发行时间，不用设置
        'exp' => 7*24*60*60,  // 过期时间，默认7天
        'sub' => 'LerHoo',    // 标题
        'nbf' => '',  // 不能在此时间之前使用
        'jti' => '',    // JWT ID
    ],

];
