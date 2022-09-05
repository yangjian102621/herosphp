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
use HerosUpload\FileSaveQiniuHandler;

#[Controller(name: UploadAction::class)]
class UploadController extends BaseController
{
    #[Get(uri: '/upload', desc: 'upload demo')]
    public function upload(): HttpResponse
    {
        return $this->view('upload');
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

    #[Post(uri: '/upload/qiniu')]
    public function qiniu(HttpRequest $request)
    {
        $config = [
            'save_handler' => FileSaveQiniuHandler::class,
            'handler_config' => [
                'access_key' => 'GNe0CvAqccBXHBJnwEoBDn-MGx607CHViYZ_ZSyj',
                'secret_key' => '0fGtlEBG7phDSnlr_BkZ3pFxsmCzJHRsIaHekX-Y',
                'bucket' => 'kindeditor',
                'domain' => 'http://nk.img.r9it.com/',
            ],
        ];
        $file = new UploadFile($config);
        $info = $file->upload($request->file('src'));
        if ($file->getUploadErrNo() === UploadError::SUCCESS) {
            return GF::exportVar($info);
        }
        return '文件上传失败.';
    }

    #[Get(uri: '/upload/base64')]
    public function base64(): HttpResponse
    {
        return $this->view('upload-base64');
    }

    #[Post(uri: '/upload/base64/do')]
    public function doBase64Upload(HttpRequest $request)
    {
        $config = [
            'save_handler' => FileSaveLocalHandler::class,
            'handler_config' => ['upload_dir' => RUNTIME_PATH . 'upload'],
        ];
        $file = new UploadFile($config);
        $info = $file->uploadBase64($request->post('img'));

        if ($file->getUploadErrNo() === UploadError::SUCCESS) {
            return GF::exportVar($info);
        }
        return '文件上传失败.';
    }
}
