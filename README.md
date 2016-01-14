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
* git2 http://git.fiidee.com/git/fiidee-php/herosphp.git


#版本更新记录

version 2.1.0
--

>

1. 根据网友的建议，又重新调整了URL结构，把/user_home_index/userid-100.shtml 重新还原成 /user/home/index/userid-100.shtml结构，更符合大家的使用习惯
2. 精简了首页代码
3. 修改了url全局函数，修复了部分url解析参数错误的bug 
4. 更改了框架的autoload函数实现， 采用了spl_autoload_register方式实现 
5. 新增composer依赖管理支持
6. 调整了组织目录结构，使app更加扁平化，模块更加独立，利于拆装。app根目录下只有configs(配置), models(数据模型)，modules（模块），支持多app，app之间相互独立，但是共享framework。


version 2.0.0
--
对框架进行了局部重构
--
>
1. 调整了URL结构，采用 /user_home_index/userid-100.shtml 代替了以前的 /user/home/index/userid-100.shtml结构，减少了目录级数，对SEO更为友好
2. 新增了WebApplication 层来控制整个web请求的生命周期，控制器中的每个功能方法都需要传入HttpRequest对象
3. 在根目录下新增了client.php 和 client 目录， 方便执行php的客户端程序。 使用方法详情见操作手册
4. 新增Beans对象管理工具，可以很方便的配置和管理服务。
5. 修改了ImageThumb 类，新增了图片裁剪方法。
6. 修改了数据操作模块，新增了对数据库集群的支持，只需要在herosp.const.php中配置 将DB_ACCESS的值改成B_ACCESS_CLUSTERS
7. 在utils中新增了邮件发送服务类 Smtp.class.php
8. 重写了session， 新增了memcache介质存储
9. 修复了文章列表页分页数据重复bug

version 1.0.0
--
实现了框架的基本功能
--
>
1. 实现了项目组织结构
2. 实现了URL解析
3. 实现数据DB层的操作
4. 实现MVC设计模式
5. 完成基本工具类，如果文件上传，图片裁剪，生成缩略图，文件处理等
