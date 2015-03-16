<?php

namespace herosphp\utils;

/*---------------------------------------------------------------------
 * 给图片添加水印, 支持jpg, png, gif格式的图片.<br />
 * 支持图片水印和文字水印， 有7种水印字体可选。
 * @package herosphp\utils
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@163.com>
 *-----------------------------------------------------------------------*/

class ImageWater {

    /**
     * @var ImageWater 图片处理的唯一实例
     */
    private static $_INSTANCE = NULL;

    /**
     * @var Image resource 原图图片资源
     */
    protected $imgSrc = NULL;

    /**
     * @var Image resource 目标图片资源
     */
    protected $imgDst = NULL;

    /**
     * @var int 水印位(置默认在右下角)
     */
    protected $position = 2;

    /**
     * @var int 水印字体
     */
    protected $font = 1;

    /**
     * @var int 水印透明度
     */
    protected $alpha = 80;

    /**
     * @var string 图片后缀
     */
    protected $extension = '';

    /**
     * @var int 水印字体大小
     */
    protected $fontSize = 30;

    /**
     * @var int 水印文字的倾斜读
     */
    protected $angle = 0;

    /**
     * @var array 水印字体颜色(默认深红色#cc0000)
     */
    protected $fontColor = array(210, 0, 0);

    /**
     * water font map (水印字体映射表)
     * 想要添加字体的用户只需在../fonts 文件夹中添加字体文件，然后在这数组中添加与字体名称
     */
    public static $_FONT_MAP = array(
        '0' 	=>  'suti.ttf', 			//新苏鹅卵石体
        '1' 	=>  'mao-ze-dong.ttf', 		//毛泽东体
        '2' 	=>  'hanyi-xiu-ying.ttf',	//汉仪秀英繁体
        '3' 	=> 	'wending-huocai.ttf', 		//文鼎火柴体 (英文字体)
        '4'		=>  'ruixian-jt.ttf',		//张海山锐线体简
        '5'		=>  'hanyi-zhujie.ttf',		//汉仪竹节繁体
        '6'		=>  'ye-jing.ttf'			//液晶体(英文字体)
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

        $this->extension = self::getFileExt($_img_src);
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
        //保存图片, 覆盖原图
        return $this->saveImage($_img_src);
    }

    /**
     * 给图片添加文字水印
     * @param        string $_img_src 原图片的路径
     * @param        string $_water_str 水印文字
     * @return      boolean
     */
    public function stringWaterMark( $_img_src, $_water_str ) {

        $this->extension = self::getFileExt($_img_src);
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

        //保存图片，覆盖原图
        return $this->saveImage($_img_src);

    }

    /**
     * sava image
     * @param    string $filename 保存新的文件名称
     * @param    int $quality 图片质量，仅对jpeg图像有效
     * @return   boolean
     */
    public function saveImage($filename, $quality = 90) {

        switch ( $this->extension ) {

            case 'jpg':
            case 'jpeg':
                return imagejpeg($this->imgDst, $filename, $quality);

            case 'gif':
                return imagegif($this->imgDst, $filename);

            case 'png':
                return imagepng($this->imgDst, $filename);

        }

        return false;

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
    protected function  &getImageSource( $_filename ) {

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
     * @param int $font
     */
    public function setFont($font)
    {
        $this->font = $font;
    }

    /**
     * @param int $fontSize
     */
    public function setFontSize($fontSize)
    {
        $this->fontSize = $fontSize;
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
     */
    public function setAlpha($alpha)
    {
        $this->alpha = $alpha;
    }

    /**
     * @param array $fontColor
     */
    public function setFontColor($fontColor)
    {
        $this->fontColor = $fontColor;
    }

    /**
     * @param int $angle
     */
    public function setAngle($angle)
    {
        $this->angle = $angle;
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