<?php

namespace app\controller;

use herosphp\annotation\Controller;
use herosphp\annotation\Get;
use herosphp\annotation\Post;
use herosphp\core\BaseController;
use herosphp\core\HttpRequest;
use herosphp\core\HttpResponse;
use herosphp\GF;
use herosphp\upload\FileSaveLocalHandler;
use herosphp\upload\UploadError;
use herosphp\upload\UploadFile;

#[Controller(name: UploadAction::class)]
class UploadController extends BaseController
{
    #[Get(uri: '/upload', desc: 'upload demo')]
    public function upload(): HttpResponse
    {
        return $this->view('upload', []);
    }

    #[Post(uri: '/upload/do')]
    public function doUpload(HttpRequest $request)
    {
        $config = [
            'save_handler' => FileSaveLocalHandler::class,
            'handler_config' => ['upload_dir' => RUNTIME_PATH . 'upload'],
        ];
        $file = new UploadFile($config);
        $info = $file->upload($request->file('src'));
        if ($file->getUploadErrNo() === UploadError::SUCCESS) {
            return GF::exportVar($info);
        }
        return '文件上传失败.';
    }
}
