#### 注意，这个项目只是框架内核代码，只能作为依赖，不能直接运行，使用 DEMO 请移步 [http://git.oschina.net/blackfox/herosphp-app](http://git.oschina.net/blackfox/herosphp-app)

为什么要造这个轮子？
====
"PHP是世界上最好的变成语言，没有之一。"这句话虽然是一个梗但是他却代表了php这门语言在编程语言中的江湖地位。那既然是最好的语言，那么免不了就会出现各种各样的框架啦，包括php官方的ZendFrame, Laravel,Yii,包括国产的ThinkPHP,ci等框架。那既然有这么多框架，为什么我们还要造这个轮子呢。原因有以下几点：

1. 最初是基于教学的目的，想作为一个学习型的框架给那些想开发自己的php框架的码农借鉴使用，抛砖引玉。
2. 目前开源的php框架总有这样或者那样的不符合公司项目要求的，使着不顺手，要么就过于重量级，像ZendFrame,ThinkPHP那样太过于臃肿，学习成本太高，要么就是扩展性和或性能达不到要求，使用起来也很不方便。
3. 想把自己学习到的php编程知识做下沉淀，那最好的方法莫过于写php开发框架了。
4. 目前的开源框架都是为了兼容高中低端各种用户而牺牲了框架本身的性能和特性，这显然没有办法完全满足公司的全部需求，而修改像thinkPHP这种大型框架是一件很痛苦的事情，跟自己开发的成本差不多。这估计也是很多大点的公司都有自己的框架的原因了。

------------------

HerosPHP的设计思想
====
HerosPHP是一套web应用开发框架，我们觉得好开发框架应该是<strong>方便，快捷，优雅，干净</strong>的，这也是我们一直在用心做的事情。我们希望框架既能封装良好，是开发者使用方便，但又不损失框架性能，既能快速搭建系统又能保持良好的扩展性。

> 作为一群有逼格的码农，我们目标是开发能够应对<code class="scode">百万级pv</code>的系统框架，并且能够支持多应用。

我们严格遵循约定优于配置的设计原则，能有约定就不提供配置，比如对一些你几乎不会配置的可配置选项，我们都会屏蔽配置接口，按照约定的方式执行，尤其是是4.0.0之后，这个原则会更加明显。配置太多只会是系统越来越臃肿，执行效率越来越低。

我们始终坚持<strong>一个问题只保留一种你能够实现的最优解决方案即可</strong>，所以我们屏蔽了一些不规范的编码习惯，比如说mysql的查询条件几乎所有的框架都兼容了直接写条件的sql语句去查询，但是在herosphp中，对不起，你只能使用我们提供的数组查询语法，这样的设计是第一是为了使代码可读性更高，调理清晰。二是为了兼容mongodb和elasticsearch的查询语法，使得代码更健壮。当然这个见仁见智，认同点赞，不喜勿喷。这里只是举个栗子而已，具体设计请阅读<code class="scode">查询语法</code>。当然，框架里面短期内还是会兼容部分的老的API，但是我们强烈推荐使用新的API。

像其他框架一样，我们也会提供redis（缓存），Annotation（注解），files（文件处理），session（会话），middleware (中间件) 等开发过程中常用的工具的功能，跟其他框架不一样的是，我们还提供了类似beans的模块，借鉴java中spring框架的模式实现服务资源的托管...

herosphp的特性
=======
1. 部署简单，高开发效率并且高性能
2. 框架的原理简单易懂，容易学习
3. 在保持约定大于配置的原则下又保持着很好的扩展性
4. 代码风格简洁漂亮，程序精简高效，但是注释详细，适合新手学习.


GitHub 源码地址
====
码云: [http://git.oschina.net/blackfox/herosphp](http://git.oschina.net/blackfox/herosphp)

GitHub: [https://github.com/yangjian102621/herosphp](https://github.com/yangjian102621/herosphp)

开发手册
========
http://docs.r9it.com/herosphp/v4.0/


demo 演示地址
=======
### [http://herosphp.r9it.com](http://herosphp.r9it.com)

联系作者
=====
邮箱：<a href="mailto:yangjian102621@gmail.com">yangjian102621@gmail.com</a>

QQ：906388445

技术交流QQ群：137352247

博客 : <a href="http://r9it.com/">小一辈无产阶级码农</a>

#### 本地调试
如果你想在本地直接调试herosphp框架，不想每次都要更新到 composer 仓库，然后再用 composer update 去composer仓库去更新。
（这个过程慢的要死，就算你使用了中国镜像也慢）
你可以通过任何方式将 herosphp 放入你的本地目录，如/code/php/herosphp
然后在你的 app 项目的 composer.json 中加入如下配置

```bash
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