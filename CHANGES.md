#版本更新记录
@3.0.0
> 1. 新增了artisan文件，作为命令行的入口文件，并实现了artisan的一些常用功能，比如创建数据库，数据表，Controller, Service, Model等。
> 2. 更新了session模块，新增了手动GC回收，目前只针对了FileSession做了实现。通过调用Session::gc()就可以进行GC回收了。

@2.0.0
------
> 进行了大版本的重构，主要是项目结构

1. 将应用和框架进一步完全拆分，包括所有的configs(配置文件)全部移动到app文件夹，包括index.php, client.php,用户只需要关注app文件夹里面的内容就好了，而不需要再去关注
框架里面的内容<br />
2. 更新了framework的文件结构，将utils里面的一些类进行了细分，新增files，image，web等文件夹，比如FileUtils, FileUpload等类文件放入files包。<br />
3. 重构了framework的model层，重写C_Model#getItems, getItem方法等，使用传入将所有的参数通过传入数组类型的作为查询条件，为后期兼容mongodb和elasticsearch查询语法做准备。
4. 添加了mongodb数据模型的实现MongoModel, 通过继承MongoModel模型来实现对mongodb的操作
4. 在framework中新增了lock包，实现了同步锁的功能，通过锁工厂SynLockFactory::getFileSynLock和SynLockFactory::getSemSynLock方法获取。
5. 在StringUtils中实现了分布式UUID的生成。调用StringUtils::genGlobalUid()获取。
6. 修复数据模型过滤器Filter的sql单引号转义bug
6. 分别给cache模块和session模块添加了redis实现（实现了redis缓存和redis session）
7. 优化流程，在WebApplication中添加AppError对象属性，用来处理整个生命周期中的错误信息，在整个生命周期都可以使用
WebApplication::getInstance()->getAppError()->setMessage(),getMessage(), getCode(), setCode()来操作错误信息和错误代码
8. 优化filter的报错信息处理，可以分别配置非空，数据长度，数据类型的报错信息。

过滤器Filter的使用
-------
#### 1. 在Model中添加以下代码，配置过滤器和报错信息
```php
$filterMap = array(
        'name' => array(Filter::DFILTER_STRING, array(6, 12), Filter::DFILTER_SANITIZE_TRIM,
            array("require" => "名字不能为空.", "length" => "名字长度必需在6-12之间.")),
        'email' => array(Filter::DFILTER_EMAIL, NULL, NULL,
            array("type" => "请输入正确的邮箱地址.")),
        'mobile' => array(Filter::DFILTER_MOBILE, NULL, NULL,
            array("type" => "请输入正确的手机号码.")),
        'id_number' => array(Filter::DFILTER_IDENTIRY, NULL, NULL,
            array('type' => '请输入正确的身份证号码.')),
        'content' => array(Filter::DFILTER_STRING, NULL, Filter::DFILTER_MAGIC_QUOTES|Filter::DFILTER_SANITIZE_HTML,
            array("require" => "个人简介不能为空."))
    );

    $this->setFilterMap($filterMap);
```
##### 具体配置参数的含义请参照herosphp\filter\Filter类, 主要分三部分，数据类型，数据长度，数据净化。
```php
	//数据类型
    const DFILTER_LATIN = 1;  //简单字符
    const DFILTER_URL = 2;    //url
    const DFILTER_EMAIL = 4;    //email
    const DFILTER_NUMERIC = 8;    //数字
    const DFILTER_STRING = 16;    //字符串
    const DFILTER_MOBILE = 32;    //手机号码
    const DFILTER_TEL = 64;    //电话号码
    const DFILTER_IDENTIRY = 128;    //身份证
    const DFILTER_REGEXP = 256;    //正则表达式
	const DFILTER_ZIP = 1024;    //邮编

    //数据的净化
    const DFILTER_SANITIZE_TRIM = 1;    //去空格
    const DFILTER_SANITIZE_SCRIPT = 2;    //去除javascript脚本
    const DFILTER_SANITIZE_HTML = 4;    //去除html标签
    const DFILTER_MAGIC_QUOTES = 8;    //去除sql注入
    const DFILTER_SANITIZE_INT = 16;    //转整数
    const DFILTER_SANITIZE_FLOAT = 32;    //转浮点数
```

