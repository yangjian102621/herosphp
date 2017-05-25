<?php

namespace herosphp\image;

/*---------------------------------------------------------------------
 * 给图片添加水印, 支持jpg, png, gif格式的图片.<br />
 * 支持图片水印和文字水印， 有7种水印字体可选。
 * @package herosphp\utils
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
 *-----------------------------------------------------------------------*/

use herosphp\string\StringUtils;

class ImageWater {

    /**
     * @var ImageWater 图片处理的唯一实例
     */
    private static $_INSTANCE = NULL;

    /**
     * @var Image resource 原图图片资源
     */
    protected $imgSrc = null;

    /**
     * @var Image resource 目标图片资源
     */
    protected $imgDst = null;

    private $fileSrc = null; //源图片路径

    protected $position = 2;  //水印位(置默认在右下角)

    protected $font = 'YaHei'; //水印字体

    protected $alpha = 80; //水印透明度

    protected $extension = ''; //图片后缀

    protected $fontSize = 30; //水印字体大小

    protected $angle = 0; //水印文字倾斜角度

    protected $fontColor = array(255, 255, 255); //水印字体颜色

    /**
     * water font map (水印字体映射表)
     * 想要添加字体的用户只需在../fonts 文件夹中添加字体文件，然后在这数组中添加与字体名称
     */
    public static $_FONT_MAP = array(
        'YaHei' 	=>  'YaHei.ttf', 			//微软雅黑
        'YaHeiBold' 	=>  'YaHeiBold.ttf', 			//微软雅黑粗体
        'ZhengHei' 	=>  'ZhengHei.ttf', 			//微软正黑
    );

    /**
     * 私有化构造方法
     */
    private function __construct() {}

    /**
     * 获取实例
     * @return ImageWater
     */
    public static function getInstance() {
        if ( self::$_INSTANCE == NULL )
            self::$_INSTANCE = new self();
        return self::$_INSTANCE;
    }

    /**
     * 给图片图片水印
     * @param 		string 		$_img_src  	 	原图片的路径
     * @param 		string 		$_water_path 	水印图片的路径
     * @return       boolean
     */
    public function imageWaterMark( $_img_src, $_water_path) {

        $this->fileSrc = $_img_src;
        $sizeDst = $this->getImageSize($_img_src);

        //创建画布
        $this->imgDst = $this->getImageTrueColor($sizeDst[0], $sizeDst[1], $this->extension);
        $this->imgSrc = $this->getImageSource($_img_src);

        //将图像载入画布
        imagecopyresampled($this->imgDst, $this->imgSrc, 0, 0, 0, 0, $sizeDst[0], $sizeDst[1], $sizeDst[0], $sizeDst[1]);

        $imgWater = $this->getImageSource($_water_path);
        $waterSize = $this->getImageSize($_water_path);	//获取水印大小
        $waterPosition = $this->getWaterPos($sizeDst, $waterSize);	//获取水印位置
        if ( $this->imgDst && $imgWater ) {
            imagecopymerge($this->imgDst, $imgWater, $waterPosition[0], $waterPosition[1], 0, 0, $waterSize[0], $waterSize[1], $this->alpha);
        }

    }

    /**
     * 给图片添加文字水印
     * @param        string $_img_src 原图片的路径
     * @param        string $_water_str 水印文字
     * @return      boolean
     */
    public function stringWaterMark( $_img_src, $_water_str ) {

        $this->fileSrc = $_img_src;
        $this->imgDst = $this->getImageSource($_img_src);
        $_color = imagecolorallocate($this->imgDst, $this->fontColor[0], $this->fontColor[1], $this->fontColor[2]);
        $sizeSrc = $this->getImageSize($_img_src);

        $_font = dirname(__FILE__).DIRECTORY_SEPARATOR.'fonts'.DIRECTORY_SEPARATOR.self::$_FONT_MAP[$this->font];

        $_ttf_box = imagettfbbox($this->fontSize, $this->angle, $_font, $_water_str);
        $_size = array(); 	//文字的尺寸
        $_size[0] = $_ttf_box[2] - $_ttf_box[0];
        $_size[1] = abs($_ttf_box[7]);
        $waterPosition = $this->getWaterPos($sizeSrc, $_size);
        if ( $this->imgDst ) {
            imagettftext($this->imgDst, $this->fontSize, $this->angle, $waterPosition[0], $waterPosition[1]+$_size[1], $_color, $_font, $_water_str);
        }

    }

    /**
     * 在图片上绘制文字
     * @param $imgFile 源图片地址
     * @param $text 文字内容
     * @param array $postionArray 文字坐标
     * @return bool
     */
    public function drawText($imgFile, $text, $postionArray) {

        if ( $imgFile ) {
            $this->fileSrc = $imgFile;
            $this->imgDst = $this->getImageSource($imgFile);
        }
        $_color = imagecolorallocate($this->imgDst, $this->fontColor[0], $this->fontColor[1], $this->fontColor[2]);
        $_font = dirname(__FILE__).DIRECTORY_SEPARATOR.'fonts'.DIRECTORY_SEPARATOR.self::$_FONT_MAP[$this->font];

        if ( is_array($text) ) {
            for ( $i = 0; $i < count($text); $i++ ) {
                $__text = $this->getEncodedText($text[$i]);
                $_ttf_box = imagettfbbox($this->fontSize, $this->angle, $_font, $__text);
                $textWidth = 0;
                $textHeight = abs($_ttf_box[7])*($i+1);
                if ( $i > 0 ) {
                    $ttfBox = imagettfbbox($this->fontSize, $this->angle, $_font, "我");
                    $textWidth -= $ttfBox[2] - $ttfBox[0];
                    $textHeight += 0.5 * abs($_ttf_box[7]);
                }
                if ( $this->imgDst ) {
                    imagettftext($this->imgDst, $this->fontSize, $this->angle, $postionArray[0]+$textWidth, $postionArray[1]+$textHeight, $_color, $_font, $__text);
                }
            }
        } else {
            $__text = $this->getEncodedText($text);
            $_ttf_box = imagettfbbox($this->fontSize, $this->angle, $_font, $__text);
            $textHeight = abs($_ttf_box[7]);
            if ( $this->imgDst ) {
                imagettftext($this->imgDst, $this->fontSize, $this->angle, $postionArray[0], $postionArray[1]+$textHeight, $_color, $_font, $__text);
            }
        }

    }

    /**
     * 绘制垂直文本
     * @param $imgFile
     * @param $text
     * @param $postionArray
     */
    public function drawVerticalText($imgFile, $text, $postionArray) {

        if ( $imgFile ) {
            $this->fileSrc = $imgFile;
            $this->imgDst = $this->getImageSource($imgFile);
        }
        $_color = imagecolorallocate($this->imgDst, $this->fontColor[0], $this->fontColor[1], $this->fontColor[2]);
        $_font = dirname(__FILE__).DIRECTORY_SEPARATOR.'fonts'.DIRECTORY_SEPARATOR.self::$_FONT_MAP[$this->font];

        if ( is_array($text) ) {
            for ( $i = 0; $i < count($text); $i++ ) {
                //估算文本尺寸
                $ttfBox = imagettfbbox($this->fontSize, $this->angle, $_font, "我");
                $lineSpace = 10; //行距
                $leftOffset= ($ttfBox[2] - $ttfBox[0]) * $i + $lineSpace*($i-1);
                $textHeight = abs($ttfBox[7]);
                if ( $this->imgDst ) {
                    $textArr = $this->getVerticalTextArray($text[$i]); //获取垂直文本数组
                    foreach( $textArr as $key => $val) {
                        imagettftext($this->imgDst, $this->fontSize, $this->angle, $postionArray[0]+$leftOffset,
                            $postionArray[1] + $textHeight * $key, $_color, $_font, $this->getEncodedText($val));
                    }
                }
            }
        } else {
            //估算文字的高度和宽度
            $_ttf_box = imagettfbbox($this->fontSize, $this->angle, $_font, $text);
            $lineSpace = 5; //行距
            $textHeight = abs($_ttf_box[7]) + $lineSpace;
            if ( $this->font == 'YaHeiBold' ) { //如果是粗体，则增加垂直文本的字间距
                $textHeight += 10;
            }
            if ( $this->imgDst ) {
                $textArr = $this->getVerticalTextArray($text); //获取垂直文本数组
                foreach( $textArr as $key => $val) {
                    imagettftext($this->imgDst, $this->fontSize, $this->angle,
                        $postionArray[0], $postionArray[1] + $textHeight * $key, $_color, $_font, $this->getEncodedText($val));
                }
            }
        }

    }

    /**
     * 获取编码后的文本
     * @param $text
     * @return mixed|string
     */
    private function getEncodedText($text) {
        return mb_convert_encoding($text, "html-entities", "utf-8");
    }

    /**
     * 获取垂直文本数组
     * @param $str
     * @return array
     */
    private function getVerticalTextArray($str) {
        $arr = array();
        for ( $i = 0; $i < strlen($str); $i++ ) {
            if ( ord($str[$i]) < 127 ) {
                array_push($arr, $str[$i]);
                continue;
            }
            if ( ord($str[$i]) > 127 &&
                ord($str[$i+1]) > 127 &&
                ord($str[$i+2]) > 127 ) {
                array_push($arr, substr($str, $i, 3));
                $i += 2;
            }
        }
        return $arr;
    }



    /**
     * sava image
     * @param    string $filename 保存新的文件名称
     * @return   boolean
     */
    public function saveImage($filename=null) {

        if ( !$filename ) $filename = $this->fileSrc;

        $extension = self::getFileExt($filename);
        switch ( $extension ) {

            case 'jpg':
            case 'jpeg':
                return imagejpeg($this->imgDst, $filename, 80);

            case 'gif':
                return imagegif($this->imgDst, $filename);

            case 'png':
                return imagepng($this->imgDst, $filename);

        }

        return false;

    }

    /**
     * show the image
     */
    public function show($filename) {
        if ( $filename ) {
            header("Content-type: image/jpeg");
            echo file_get_contents($filename);
            return;
        }

        $extension = self::getFileExt($this->fileSrc);
        switch ( $extension ) {
            case 'jpg':
            case 'jpeg':
                header("Content-type: image/jpeg");
                imagejpeg($this->imgDst);
                break;

            case 'gif':
                header("Content-type: image/gif");
                imagegif($this->imgDst);
                break;

            case 'png':
                header("Content-type: image/png");
                imagepng($this->imgDst);
                break;

            default:
                die("No image support in this PHP server");

        }
    }

    /**
     * get water postion (获取图片水印的位置)
     * @param  		array 		$_dstimg_size   	目标图片的大小
     * @param  		array 		$_water_size    水印图片的大小
     * @return     array
     */
    protected function getWaterPos( $_dstimg_size, $_water_size ) {

        $position = array();

        switch ( $this->position ) {
            case 0 :	//左上角
                $position[0] = 10;
                $position[1] = 10;
                break;

            case 1 :	//右上角
                $position[0] = ($_dstimg_size[0] - $_water_size[0]) - 10;
                $position[1] = 10;
                break;

            case 2 :	//右下角
                $position[0] = ($_dstimg_size[0] - $_water_size[0])-10;
                $position[1] = ($_dstimg_size[1] - $_water_size[1])-10;
                break;

            case 3 :	//左下角
                $position[0] = 10;
                $position[1] = ($_dstimg_size[1] - $_water_size[1]) - 10;
                break;

            case 4 : 	//居中
                $position[0] = (int) ($_dstimg_size[0] - $_water_size[0])/2;
                $position[1] = (int) ($_dstimg_size[1] - $_water_size[1])/2;
                break;

            case 5 :	//随机
                $position[0] = mt_rand(10, ($_dstimg_size[0] - $_water_size[0]) - 10);
                $position[1] = mt_rand(10, ($_dstimg_size[1] - $_water_size[1]) - 10);
                break;
        }
        return $position;
    }

    /**
     * 获取图片尺寸
     * @param $filename
     * @return array
     */
    protected function getImageSize( $filename ) {

        $info = getimagesize($filename);
        return array($info[0], $info[1]);
    }

    /**
     * 根据图片地址获取图片资源
     * @param        string $_filename 图片路径
     * @return null|resource
     */
    public function  &getImageSource( $_filename ) {

        $_img = NULL;
        $_ext = self::getFileExt($_filename);
        switch ( $_ext ) {

            case 'gif':
                $_img = imagecreatefromgif($_filename);
                break;

            case 'png':
                $_img = imagecreatefrompng($_filename);
                break;

            case 'jpg':
            case 'jpeg':
                $_img = imagecreatefromjpeg($_filename);

        }
        return $_img;
    }

    /**
     * 获取文件名后缀
     * @param $filename
     * @return string
     */
    public static function getFileExt( $filename ) {
        $_pos = strrpos( $filename, '.' );
        return strtolower( substr( $filename , $_pos+1 ) );
    }

    /**
     * 创建真彩色画布
     * @param 		int 	$_width 	 画布宽度
     * @param 		int 	$_height  	 画布高度
     * @param 		string 	$_flag 		 画布参数
     * @return      image resource       返回图片资源
     */
    protected function getImageTrueColor( $_width, $_height, $_flag ) {

        $_img = imagecreatetruecolor($_width, $_height);
        $_color = imagecolorallocate($_img, 0, 0, 0);
        switch ( $_flag ) {
            case 'gif':
                imagecolortransparent($_img, $_color);
                break;

            default:
                imagecolortransparent($_img, $_color);
                imagealphablending($_img, false);
                imagesavealpha($_img, true);
                break;
        }
        return $_img;
    }

    /**
     * @param string $font
     * @return $this
     */
    public function setFont($font)
    {
        $this->font = $font;
        return $this;
    }

    /**
     * @param int $fontSize
     * @return $this
     */
    public function setFontSize($fontSize)
    {
        $this->fontSize = $fontSize;
        return $this;
    }

    /**
     * @param int $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @param int $alpha
     * @return $this
     */
    public function setAlpha($alpha)
    {
        $this->alpha = $alpha;
        return $this;
    }

    /**
     * @param array $fontColor
     * @return $this
     */
    public function setFontColor($fontColor)
    {
        $this->fontColor = $fontColor;
        return $this;
    }

    /**
     * @param int $angle
     * @return $this
     */
    public function setAngle($angle)
    {
        $this->angle = $angle;
        return $this;
    }

    /**
     * 销毁图片资源，释放内存
     */
    public function __distruct() {

        if ( $this->imgSrc )
            imagedestroy($this->imgSrc);

        if ( $this->imgDst )
            imagedestroy($this->imgDst);

    }

}
?>