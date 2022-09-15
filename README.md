### 注意，这个项目只是框架内核代码，只能作为依赖，不能直接运行!!!

herophp demo 项目在这里：[http://git.oschina.net/blackfox/herosphp-app](http://git.oschina.net/blackfox/herosphp-app)

或者你可以通过 `composer` 直接创建：

```bash
composer create-project herosphp/app demo # demo 指代应用名称（App Name）
```

## 为什么要造这个轮子？

"PHP是世界上最好的变成语言，没有之一。" 这句话虽然是一个梗但是他却代表了php这门语言在编程语言中的江湖地位。
既然是最好的语言，那么免不了就会出现各种各样的框架啦，包括php官方的ZendFrame, Laravel,Yii,包括国产的ThinkPHP,ci等框架。

那既然有这么多框架，为什么我们还要造这个轮子呢？原因有以下几点：

1. 最初是基于教学的目的，这个项目脱胎于本人做 PHP 培训的时候给学员演示 Web 框架开发的学习项目，后面经过一系列完善成现在这个样子。
2. 当时开源的 PHP 框架要么就过于重量级，像ZendFrame,ThinkPHP那样太过于臃肿，学习成本太高，要么就是扩展性和或性能达不到要求，使用起来不那么方便。
3. 大部分 PHP 开源框架都是为了兼容高中低端各种用户而牺牲了框架本身的性能和特性，这显然没有办法完全满足公司的全部需求，而个人觉得修改大型框架是一件很痛苦的事情，跟自己开发的成本差不多。这估计也是很多大点的公司都有自己的框架的原因了。

## HerosPHP 的设计思想
1. HerosPHP 是一套web应用开发框架，我们觉得好开发框架应该是**方便，快捷，优雅，干净的**，我们一直坚持：**一个体验良好的工具应该是操作简单的，同时又是性能可靠的** 的原则。
我们希望框架既能封装良好，降低开发者学习成本，同时又不损失系统性能，既能快速搭建系统又能保持良好的扩展性，目前我们最新的版本已经基本做到这一点。

2. 严格遵循约定优于配置的设计原则，如非必需不提供配置，直接按照最优配置实现，尤其是是4.0.0之后，这个原则会更加明显，我们认为配置太多只会是系统越来越臃肿，执行效率越来越低。

3. 始终坚持 **一个问题只保留一种你能够实现的最优解决方案** + **最小框架内核原则**，框架只实现最基本的功能，附加功能（比如文件上传，ORM，中间件等）都通过 `composer` 组件来实现，按需引入。

4. 像其他框架一样，我们也会提供redis（缓存），Annotation（注解），files（文件处理），session（会话），middleware (中间件) 等开发过程中常用的工具的功能，跟其他框架不一样的是，我们还提供了类似beans的模块，借鉴java中spring框架的模式实现服务资源的托管...

## HerosPHP 的特性
1. 框架的原理简单易懂，使用和学习的成本低，想要二开也非常容易上手。
2. 使用注解的方式实现路由，提供 Bean 容器，实现服务的自动注入，如丝般顺滑，提升开发效率。
3. 良好的设计架构，保持性能强悍的同时，兼具良好的扩展性。
4. 代码风格简洁漂亮，程序精简（内核代码不到 200K），注释详细，适合新手研究学习。


## GitHub 源码地址

码云: [http://git.oschina.net/blackfox/herosphp](http://git.oschina.net/blackfox/herosphp)

GitHub: [https://github.com/yangjian102621/herosphp](https://github.com/yangjian102621/herosphp)

## 开发手册

最新的开发手册正在整理中...

## 联系作者

邮箱：<a href="mailto:yangjian102621@gmail.com">yangjian102621@gmail.com</a>

QQ：906388445

技术交流QQ群：137352247

博客 : [小一辈无产阶级码农](https://www.r9it.com)

## 本地调试
你可以通过任何方式将 herosphp 放入你的本地目录，如/code/php/herosphp
然后在你的 app 项目的 composer.json 中加入如下配置

```json
"repositories": [
        {
            "type": "path",
            "url": "/code/php/herosphp"
        }
    ],
```

然后在 app 根目录下运行

```bash
composer require 'herosphp/framework:*@dev'
```