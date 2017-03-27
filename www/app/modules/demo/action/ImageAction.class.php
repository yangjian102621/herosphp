<?php
namespace demo\action;

use herosphp\core\Controller;
use herosphp\http\HttpRequest;
use herosphp\image\ImageThumb;
use herosphp\image\VerifyCode;

/**
 * 图片测试
 * @since           2015-01-28
 * @author          yangjian<yangjian102621@gmail.com>
 */
class ImageAction extends Controller {

    //生成验证码
    public function scode( HttpRequest $request ) {
        //画布设置
        $config = array('x'=>10, 'y'=>30, 'w'=>120, 'h'=>50, 'f'=>22);
        $verify = VerifyCode::getInstance();
        $verify->configure($config)->generate(5); //产生5位字符串
        $verify->show('png');
        die();
    }

    //生成缩略图
    public function thumb() {

        $thumb = ImageThumb::getInstance();
        $thumb->makeThumb(array(100, 100), 'src.jpg', 'dist.jpg', false);
        $thumb->crop(array(0,0), array(100,100), 'src.jpg', 'crop.jpg', false);
        $thumb->showImage();
    }
  
}
