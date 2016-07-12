>HerosPHP高性能php开发框架<br />
@author 阳建 yangjian102621@163.com <br />
@since 2014-05-13

HerosPHP是一个轻量级PHP web 程序开发框架。作者开发这个框架的初衷是想作为一个学习型的框架给那些想深入了解框架并想自己开发框架的phper借鉴用的，也可以作为中小型网站的开发提高效率。她有如下特点 

1. 部署简单，高开发效率并且高性能
2. 框架的原理简单易懂，容易学习
3. 在保持约定大于配置的原则下又保持着很好的扩展性
4. 代码风格简洁漂亮，程序精简高效，但是注释详细，适合新手学习

<br/>
在作者学习java的Web程序开发之后立即将java的一些开发模式和设计思想引入了herosphp框架，对框架进行了一次内核的重构，将框架升级到了2.0，herosphp2.0版本对开发大中型的系统提供了很多便利。引入Beans的开发模式，具体解释清看版本更新日志

#项目地址
* git1 https://git.oschina.net/blackfox/herosphp.git

#版本更新记录
@latest
------

* 修复数据模型过滤器Filter的sql单引号转义bug
* 优化流程，在WebApplication中添加AppError对象属性，用来处理整个生命周期中的错误信息，在整个生命周期都可以使用
WebApplication::getInstance()->getAppError()->setMessage(),getMessage(), getCode(), setCode()来操作错误信息和错误代码
* 优化filter的报错信息处理，可以分别配置非空，数据长度，数据类型的报错信息。

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



version 2.1.1
------
* 修复一些已知的bug；

*  去掉phpunit支持；

*  新增gmodel自动构建的基础功能，根据用户编写的xml文档自动创建数据库，数据表，索引，Model, Dao, Service，以及Controller；

*  集成了前端框架bootstrap的一个优秀的后台模板，并抽离了一些不常用的插件，使其轻量化。

gmodel使用
-----
#####1. 新建app/xml/{module}.xml, 其中{module}表示模块
```xml
<root dbhost="localhost" dbuser="root" dbpass="123456" dbname="herosphpTest" charset="utf8"
      table-prefix="fiidee_" author="yangjian" module="user" email="yangjian102621@gmail.com">

  <!-- table config -->
  <table name="user" comment="用户表" engine="InnoDB" action-name="user">
    <pk name="id" type="int(11)" ai="true" />

    <fields>
      <field name="username" type="varchar(32)" default="" comment="用户名" add-index="true" index-type="unique" />
      <field name="password" type="varchar(32)" default="" comment="密码" />
      <field name="sex" type="char(1)" default="" comment="性别" />
      <field name="addtime" type="timestamp" default="CURRENT_TIMESTAMP" comment="添加时间" />
    </fields>

  </table>

  <table name="news" comment="新闻表" engine="InnoDB" action-name="news">
    <pk name="id" type="int(11)" ai="true" />

    <fields>
      <field name="title" type="varchar(100)" default="" comment="标题" add-index="true" index-type="normal" />
      <field name="bcontent" type="varchar(255)" default="" comment="描述" />
      <field name="addtime" type="tinyint(2)" comment="添加时间" />
    </fields>
  </table>

  <table name="admin" comment="管理员表" engine="InnoDB" action-name="admin">
    <pk name="id" type="int(11)" ai="true" />

    <fields>
      <field name="user" type="varchar(32)" default="" comment="用户名" add-index="true" index-type="unique" />
      <field name="pass" type="varchar(32)" default="" comment="密码" />
      <field name="role_id" type="tinyint(4)" default="0" comment="角色ID" />
      <field name="addtime" type="timestamp" comment="添加时间" />
      <field name="edittime" type="timestamp" comment="更新时间" />
    </fields>
  </table>

  <!-- service config -->
  <!-- Attention: when a service depends more than one models, the main model should be the first model,
  for example model="user,news,admin", 'user' is the main model. -->
  <service-config>
    <service name="UserService" dao="UserDao" model="user,news,admin" />
    <service name="NewsService" dao="NewsDao" model="news" />
    <service name="AdminService" dao="AdminDao" model="admin" />
  </service-config>
</root>
```
#####2. 在框架根目录执行
```shell
php client.php gmodel user table
```
其中 user 表示{module}, 模块名称，也就是xml文件名， table 表示生成数据表，具体对应如下
* table => 生成数据表
* model => 生成model层
* dao   => 生成dao层
* service => 生成服务层
* controller => 生成控制器
* --all => 生成全部

###对，就是这么简单！
******
<br />
version 2.1.1
--

* composer.json中加入了workerman 和phpoffice 插件

*  添加phpunit支持

*  修复分页类url传参bug，支持常规传参和伪静态传参

*  修复getConfig()函数在使用命令行调用时获取不到配置的bug

*  新增异常捕获，当关闭debug模式时，如果系统在运行的过程中抛出异常会自动记录到错误日志/runtime/app下

*  更改过滤器，新增浮点型的数据过滤

*  更新控制器中的 setView() 方法，添加支持设置其他模块的试图模块,更新异常处理函数 E()， 直接抛出异常，不用die掉


version 2.1.0
--

* 根据网友的建议，又重新调整了URL结构，把/user_home_index/userid-100.shtml 重新还原成 /user/home/index/userid-100.shtml结构，更符合大家的使用习惯

*  精简了首页代码

*  修改了url全局函数，修复了部分url解析参数错误的bug 

*  更改了框架的autoload函数实现， 采用了spl_autoload_register方式实现 

*  新增composer依赖管理支持

*  调整了组织目录结构，使app更加扁平化，模块更加独立，利于拆装。app根目录下只有configs(配置), models(数据模型)，modules（模块），支持多app，app之间相互独立，但是共享framework。


version 2.0.0
--

<strong>对框架进行了局部重构</strong>

* 调整了URL结构，采用 /user_home_index/userid-100.shtml 代替了以前的 /user/home/index/userid-100.shtml结构，减少了目录级数，对SEO更为友好

*  新增了WebApplication 层来控制整个web请求的生命周期，控制器中的每个功能方法都需要传入HttpRequest对象

*  在根目录下新增了client.php 和 client 目录， 方便执行php的客户端程序。 使用方法详情见操作手册

*  新增Beans对象管理工具，可以很方便的配置和管理服务。

*  修改了ImageThumb 类，新增了图片裁剪方法。

*  修改了数据操作模块，新增了对数据库集群的支持，只需要在herosp.const.php中配置 将DB_ACCESS的值改成B_ACCESS_CLUSTERS

*  在utils中新增了邮件发送服务类 Smtp.class.php

*  重写了session， 新增了memcache介质存储

*  修复了文章列表页分页数据重复bug

version 1.0.0
--
<strong>实现了框架的基本功能</strong>

* 实现URL解析

*  实现数据DB层的操作

*  实现MVC设计模式

*  完成基本工具类，如果文件上传，图片裁剪，生成缩略图，文件处理等
