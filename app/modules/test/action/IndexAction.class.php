<?php
namespace test\action;

use herosphp\bean\Beans;
use herosphp\core\Controller;
use herosphp\core\Debug;
use herosphp\core\WebApplication;
use herosphp\db\entity\MysqlEntity;
use herosphp\http\HttpRequest;
use herosphp\string\StringBuffer;
use herosphp\utils\AjaxResult;
use herosphp\web\WebUtils;
use Workerman\Worker;

/**
 * 首页测试
 * @since           2015-01-28
 * @author          yangjian<yangjian102621@gmail.com>
 */
class IndexAction extends Controller {

    /**
     * 首页方法
     * @param HttpRequest $request
     */
    public function index( HttpRequest $request ) {
        $sb = new StringBuffer("<?php\n");
        $sb->appendLine('namespace app\\action');
        $sb->appendLine('class StringBuffer {');
        $sb->appendTab('public function __construct($str);', 1);
        $sb->appendTab('public function isEmpty();', 1);
        $sb->appendLine("}");
        echo $sb->toString();
        die('xxxxxxx');
    }

    //获取用户列表
    public function user() {

        $service = Beans::get('test.user.service');

        $query = MysqlEntity::getInstance()->field('*')->addOptWhere('id', '>=', 20)->addLikeWhere('username', '123');

        $result = $service->getItems($query);
        __print($result);
        die();

    }
  
}
?>
