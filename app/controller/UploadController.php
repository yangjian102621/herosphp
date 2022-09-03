<?php

namespace app\controller;

use herosphp\annotation\Controller;
use herosphp\annotation\Get;
use herosphp\annotation\Post;
use herosphp\core\HttpRequest;
use herosphp\core\UploadFile;
use herosphp\utils\FileUtil;
use herosphp\utils\StringUtil;

#[Controller(name: UploadAction::class)]
class UploadController
{
    #[Get(uri: '/upload', desc: 'upload demo')]
    public function upload(HttpRequest $request): string
    {
        //src 数组 或者 单个文件
        $uploadFiles = $request->upload('src');
        if ($uploadFiles) {
            $tempPath = RUNTIME_PATH . '/tmp/';
            FileUtil::makeFileDirs($tempPath);
            if (is_array($uploadFiles)) {
                /** @var UploadFile $uploadFile*/
                foreach ($uploadFiles as $uploadFile) {
                    $newName = StringUtil::genGlobalUid() . '.' . $uploadFile->getUploadExtension();
                    $uploadFile->move($tempPath . $newName);
                }
            } else {
                $newName = StringUtil::genGlobalUid() . '.' . $uploadFiles->getUploadExtension();
                $uploadFiles->move($tempPath . $newName);
            }
        }
        return 'ok';
    }
}
