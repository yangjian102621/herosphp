<?php
namespace app\demo\action;

use herosphp\core\Controller;
use herosphp\files\FileUpload;
use herosphp\http\HttpRequest;
use herosphp\utils\JsonResult;

/**
 * 文件上传测试
 * @author          yangjian<yangjian102621@gmail.com>
 */
class UploadAction extends Controller {

    /**
     * 上传页面
     * @param HttpRequest $request
     */
    public function index(HttpRequest $request) {

        $this->setView('upload');

    }

    /**
     * 上传文件处理
     * @param HttpRequest $request
     */
    public function upload(HttpRequest $request) {

        $dir = "/static/upload/".date('Y')."/".date('m');
        $config = array(
            "upload_dir" => APP_ROOT.$dir,
            //允许上传的文件类型
            'allow_ext' => 'jpg|jpeg|png|gif|txt|pdf|rar|zip|swf|bmp|c|java|mp3',
            //图片的最大宽度, 0没有限制
            'max_width' => 0,
            //图片的最大高度, 0没有限制
            'max_height' => 0,
            //文件的最大尺寸
            'max_size' =>  1020*1024 * 10,     /* 文件size的最大 1MB */
        );
        $upload = new FileUpload($config);
        $result = $upload->upload('src');
        if ( $result ) {
            JsonResult::result(200, "/res/{$dir}/".$result['file_name']);
        } else {
            JsonResult::result(201, $upload->getUploadMessage());
        }
    }

}
