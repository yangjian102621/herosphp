# laravel-orm

`illuminate/database` 适配 `herosphp`

# Manual
```shell
    composer install herosphp/laravel-orm
```

```shell
<?php
declare(strict_types=1);
namespace app\bootstrap;

use herosLdb\LaravelDbStarter;
use herosphp\core\Config;

/**
 * Laravel启动器
 */
class LaravelStarter extends LaravelDbStarter
{
    //自定义分页参数名称
    //protected static string $pageName = "page";
    
    //是否终端输出sql
    //protected static bool $debugSQL = true;
}

LaravelStarter::init(Config::get(name:'database',default: []));
```

