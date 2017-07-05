<?php
namespace herosphp\qrcode;
/**
 * 二维码生成入口程序
 * @author yangjian
 * @email yangjian102621@gmal.com
 * @date 2017-03-21
 */
class QRcode {

    //文本类容
    private $text;
    //输出路径
    private $outfile = false;
    //尺寸
    private $size = 200;
    //边距
    private $margin = 1;
    //前景色
    private $fgcolor = '#000000';
    //背景色
    private $bgcolor = '#ffffff';
    //容错等级
    private $errroLevel = QR_ECLEVEL_L;
    //logo 文件地址, 可以是本地图片，也可以是网络图片
    private $logo = null;

    public static function png($text) {
        $instance = new self();
        $instance->text($text);
        return $instance;
    }

    /**
     * 设置文本
     * @param $text
     * @return $this
     */
    public function text($text) {
        $this->text = $text;
        return $this;
    }

    /**
     * 设置尺寸
     * @param $size
     * @return $this
     */
    public function size($size) {
        $this->size = $size;
        return $this;
    }

    /**
     * 设置边距
     * @param $margin
     * @return $this
     */
    public function margin($margin) {
        $this->margin = $margin;
        return $this;
    }

    /**
     * 设置前景色
     * @param $fgcolor
     * @return $this
     */
    public function fgcolor($fgcolor) {
        $this->fgcolor = $fgcolor;
        return $this;
    }

    /**
     * 设置背景色
     * @param $bgcolor
     * @return $this
     */
    public function bgcolor($bgcolor) {
        $this->bgcolor = $bgcolor;
        return $this;
    }

    /**
     * 设置logo路径
     * @param $logo
     * @return $this
     */
    public function logo($logo) {
        $this->logo = $logo;
        return $this;
    }

    /**
     * 显示二维码
     */
    public function show() {
        $qrsize = $this->size/27;
        SQRcode::png(
            $this->text,
            $this->outfile,
            $this->errroLevel,
            $qrsize,
            $this->margin,
            self::hex2rgb($this->fgcolor),
            self::hex2rgb($this->bgcolor),
            $this->logo);
    }

    /**
     * 保存二维码
     * @param $outfile
     */
    public function save($outfile) {
        $this->outfile = $outfile;
        $qrsize = $this->size/27;
        SQRcode::png(
            $this->text,
            $this->outfile,
            $this->errroLevel,
            $qrsize,
            $this->margin,
            self::hex2rgb($this->fgcolor),
            self::hex2rgb($this->bgcolor),
            $this->logo);
    }


    /**
     * 将16进制的颜色转成成RGB
     * @param $hexColor
     * @return array
     */
    private static function hex2rgb($hexColor) {

        $color = str_replace('#', '', $hexColor);
        //1.六位数表示形式
        if ( strlen($color) > 3 ) {
            $rgb = array(
                'r' => hexdec(substr($color, 0, 2)),
                'g' => hexdec(substr($color, 2, 2)),
                'b' => hexdec(substr($color, 4, 2))
            );

            //2. 三位数表示形式
        } else {
            $color = $hexColor;
            $r = substr($color, 0, 1) . substr($color, 0, 1);
            $g = substr($color, 1, 1) . substr($color, 1, 1);
            $b = substr($color, 2, 1) . substr($color, 2, 1);
            $rgb = array(
                'r' => hexdec($r),
                'g' => hexdec($g),
                'b' => hexdec($b)
            );
        }
        return $rgb;
    }

}