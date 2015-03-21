<?php
namespace test\action;

use herosphp\core\Controller;
use herosphp\http\HttpRequest;
use herosphp\utils\FileUpload;

/**
 * 文件上传测试
 * @author          yangjian<yangjian102621@163.com>
 */
class UploadAction extends Controller {

    /**
     * 上传页面
     * @param HttpRequest $request
     */
    public function index( HttpRequest $request ) {

        $this->display('upload_index');

    }

    /**
     * 上传文件处理
     * @param HttpRequest $request
     */
    public function upload( HttpRequest $request ) {

        $config = array(
            "upload_dir" => RES_PATH."upload/".date('Y')."/".date('m'),

        );
        $upload = new FileUpload($config);
        $result = $upload->upload('src');
        __print($result);
        __print($upload->getUploadMessage());
        die();
    }
  
}
?>
