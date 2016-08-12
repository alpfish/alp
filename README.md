# 介绍
一个学习Laravel 5.1 的库，包括Api/JWT SNS Email 认证等


引入 Laravel Application 方法：

1. 在/composer.json 加入:
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "Alp\\": "alp/"
        },
        "files": [
            "alp/helpers.php"
        ]
    },

2. 在CMD命令行项目根目录中运行：composer dump

3. 现在可以正常使用命名空间 namespace

======================================================================================

目录结构说明：

┌── .resources   相关资源及说明，内容不被codes使用
│
├── Contracts    服务接口
│
├── Facades      服务门面
│
├── Repositories 服务相关的模型仓库（每个模型使用Contracts在服务中绑定），应用程序控制器逻辑仓库放app/Repositories目录下
│
└── Traits       服务所提供的Traits方法
