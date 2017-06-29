<?php
namespace app\demo\action;

use herosphp\core\Controller;
use herosphp\core\Loader;
use herosphp\files\PHPZip;
use herosphp\http\HttpRequest;
use herosphp\session\FileSession;
use herosphp\files\FileUtils;
use herosphp\utils\Page;

/**
 * zip压缩工具测试
 * @author          yangjian<yangjian102621@gmail.com>
 */
class ZipAction extends Controller {

    /**
     * 创建压缩文件
     * @param HttpRequest $request
     */
    public function create( HttpRequest $request ) {

        $zip = new PHPZip();
        if ( $zip->createZip('/php/xhlm', '/php/xhlm.zip') ) {
            echo '生成压缩文件成功！';
        } else {
            echo '生成压缩文件失败！';
        }
        die();

    }

    /**
     * 解压文件
     * @param HttpRequest $request
     */
    public function extract( HttpRequest $request ) {

        $zip = new PHPZip();
        if ( $zip->extractZip('/php/xhlm.zip', '/php/123') ) {
            echo('解压文件成功！');
        } else {
            echo('解压文件失败！');
        }
        die();
    }
  
}
?>
