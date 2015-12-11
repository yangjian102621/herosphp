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
            //允许上传的文件类型
            'allow_ext' => 'jpg|jpeg|png|gif|txt|pdf|rar|zip|swf|bmp|c|java|mp3',
            //图片的最大宽度, 0没有限制
            'max_width' => 0,
            //图片的最大高度, 0没有限制
            'max_height' => 0,
            //文件的最大尺寸
            'max_size' =>  1024000,     /* 文件size的最大 1MB */
        );
        $upload = new FileUpload($config);
        $result = $upload->upload('src');
        __print($result);
        __print($upload->getUploadMessage());
        die();
    }

}
?>
