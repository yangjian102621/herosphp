<?php
namespace demo\action;

use herosphp\core\Controller;
use herosphp\http\HttpRequest;
use herosphp\image\Image;
use herosphp\image\ImageThumb;
use herosphp\image\Text;
use herosphp\image\VerifyCode;

/**
 * 图片测试
 * @since           2015-01-28
 * @author          yangjian<yangjian102621@gmail.com>
 */
class ImageAction extends Controller {

    public function index(HttpRequest $request) {
        $filename = RES_PATH."test.png";
        $waterImage = RES_PATH."weixin.png";
        $image = Image::getInstance();
//        $content = array(
//            '我们曾牵着手招摇过市',
//            '在街道中心旁若无人的拥抱亲吻',
//            '我们曾大半夜不睡觉窝在被子里拿着手机聊到凌晨'
//        );
        $text = new Text("我们曾牵着手招摇过市，在街道中心旁若无人的拥抱亲吻。我们曾大半夜不睡觉窝在被子里拿着手机聊到凌晨");
        //$text = new Text($content);
        $text->setFontsize(30)
            ->setColor("#CC0000")
            ->setStartY(20)
            ->setFont(Text::FONT_YAHEI)
            ->setLineHeight(10)
            ->setAlpha(127)
            ->setVertical(false);
        //绘制文字水印
        //$image->open($filename)->addStringWater($text, Image::POS_RANDOM)->save(RES_PATH."string-water.png")->show();
        //绘制图片水印
//        $image->open($filename)->addImageWater($waterImage)->save(RES_PATH."image-water.png")->show();

        //绘制文字
        $image->open($filename)
            ->drawText($text)
            //->save(RES_PATH."draw-text.png")
            ->show();

        //生成缩略图
        //$image->open($filename)->thumb(400,200, Image::THUMB_UNIFORM_SCALE)->show();

        //图片裁剪
        //$image->open($filename)->crop(1200, 800, null, null)->show()->save(RES_PATH."crop.png");

    }

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
        $thumb->makeThumb(array(100, 100), 'src.png', 'dist.png', false);
        $thumb->crop(array(0,0), array(100,100), 'src.png', 'crop.png', false);
        $thumb->showImage();
    }
  
}
