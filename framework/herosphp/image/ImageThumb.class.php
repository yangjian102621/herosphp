<?php

namespace herosphp\image;

/*---------------------------------------------------------------------
 * Class ImageThumb 缩略图生成类。支持jpg, png, gif格式的图片
 * @package herosphp\utils
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
 *-----------------------------------------------------------------------*/

class ImageThumb {

    /**
     * @var ImageThumb 图片处理的唯一实例
     */
    protected static $_INSTANCE = NULL;

    /**
     * @var 原图resource
     */
    protected $imgSrc = NULL;

    /**
     * @var 目标图片resource
     */
    protected $imgDst = NULL;

    /**
     * @var array 原图的尺寸
     */
    protected $sizeSrc = array();

    /**
     * @var array 缩放目标图片的尺寸
     */
    protected $sizeDst = array();

    /**
     * @var string 图片的后缀
     */
    protected $extension = 'jpg';

    /**
     * @var int 缩放方式
     * 0 => 直接缩放到目标大小(默认)
     * 1 => 等比缩放到指定的size
     * 2 => 指定宽度或者高度，另一维度按照等比缩放, 必须指定某一维度，而另一维度的值为0
     * array(0, 700) 高度最大700, 宽度等比缩放
     * array(700, 0) 宽度最大700,高对等比缩放
     *
     */
    protected $flag = 0;

    /**
     * 私有化构造方法
     */
    private function __construct() {}

    /**
     * 获取缩略图的唯一实例
     * @return ImageThumb
     */
    public static function getInstance() {

        if (self::$_INSTANCE == NULL)
            self::$_INSTANCE = new self();
        return self::$_INSTANCE;
    }

    /**
     * 生成缩略图
     * @param array $size 缩略图尺寸
     * @param string $imgSrc 原图路径
     * @param string $outfile 缩略图输出文件
     * @param int $quality 图片质量(0-100)
     * @param boolean $overwrite 是否覆盖原图
     * @return  mixed
     */
    public function makeThumb($size, $imgSrc, $outfile=null, $overwrite = false, $quality=90)
    {
        $this->extension = $this->getFileExt($imgSrc);
        $this->sizeSrc = $this->getImageSize($imgSrc);
        $this->imgDst = $this->createDstImage($size); //创建目标图像资源

        //目标图片的拷贝
        $this->imgSrc = $this->getImageSource($imgSrc); //获取图片资源
        if ($this->imgSrc && $this->imgDst) {
            $result = imagecopyresampled($this->imgDst, $this->imgSrc, 0, 0, 0, 0, $this->sizeDst[0],
                $this->sizeDst[1], $this->sizeSrc[0], $this->sizeSrc[1]);

            if (!$result) return false;
        }

        //如果传入了缩略图的名称则生成指定的缩略图，否则自动生成缩略图名称
        if ( $outfile != null ) {
            $this->saveImage($outfile, $quality);
        } else {
            //覆盖原图
            if ( $overwrite ) {
                $outfile = $imgSrc;
            } else {
                $outfile = $this->getThumbFilename($imgSrc, $size);
            }
            $this->saveImage($outfile, $quality);
        }

        return $outfile;
    }

    /**
     * 裁剪图片
     * @param array $position 裁剪位置 array(x, y);
     * @param array $size 缩略图尺寸 array(w, h)
     * @param string $imgSrc 原图路径
     * @param string $outfile 缩略图输出文件
     * @param boolean $overwrite 是否覆盖原图
     * @param int $quality 图片质量(0-100)
     * @return  mixed
     */
    public function crop($position, $size, $imgSrc, $outfile=null, $overwrite=false, $quality=90)
    {
        $this->extension = $this->getFileExt($imgSrc);
        $this->sizeSrc = $this->getImageSize($imgSrc);
        $this->imgDst = $this->createDstImage($size); //创建目标图像资源

        //目标图片的拷贝
        $this->imgSrc = $this->getImageSource($imgSrc); //获取图片资源
        if ($this->imgSrc && $this->imgDst) {
            $result = imagecopyresampled($this->imgDst, $this->imgSrc, 0, 0, $position[0], $position[1], $this->sizeSrc[0],
                $this->sizeSrc[1], $this->sizeSrc[0], $this->sizeSrc[1]);

            if (!$result) return false;
        }

        //如果传入了缩略图的名称则生成指定的缩略图，否则自动生成缩略图名称
        if ( $outfile != null ) {
            $this->saveImage($outfile, $quality);
        } else {
            //覆盖原图
            if ( $overwrite ) {
                $outfile = $imgSrc;
            } else {
                $outfile = $this->getCropFilename($imgSrc, $size);
            }
            $this->saveImage($outfile, $quality);
        }
        return $outfile;
    }

    /**
     * sava image
     * @param    string $filename 保存新的文件名称
     * @param    int $quality 图片质量，仅对jpeg图像有效
     * @return  boolean
     */
    public function saveImage($filename, $quality = 75) {

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
     * 输出图片
     */
    public function showImage() {

        header("Content-Type:image/".$this->extension);
        imagejpeg($this->imgDst, NULL);
    }

    /**
     * 获取缩略图的名称
     * @param $filename 文件名称
     * @param $size 缩放尺寸
     * @return array
     */
    protected function getThumbFilename($filename, $size)
    {
        return $filename . ".{$size[0]}x{$size[1]}." . $this->extension;
    }

    /**
     * 获取cai的名称
     * @param $filename 文件名称
     * @param $size 缩放尺寸
     * @return array
     */
    protected function getCropFilename($filename, $size)
    {
        return $filename . ".__crop__.{$size[0]}x{$size[1]}." . $this->extension;
    }

    /**
     * 获取文件后缀名
     * @param $filename
     * @return string
     */
    protected function getFileExt($filename) {

        $info = pathinfo($filename);
        return $info['extension'];

    }

    /**
     * 获取图片尺寸
     * @param $filename
     * @return array
     */
    protected function getImageSize($filename)
    {
        $info = getimagesize($filename);
        return array($info[0], $info[1]);
    }

    /**
     * 创建目标图片
     * @param array $size 缩放尺寸
     * @return midex
     */
    protected function createDstImage($size)
    {
        switch ( $this->flag ) {
            //直接缩放
            case 0:
                $this->sizeDst = $size;
                break;
            //等比缩放到指定size
            case 1:
                $_ratio = $this->getZoomRatio($size); //获取缩放比例
                if ($_ratio == 0) break;
                $this->sizeDst = $this->getDstImageSize($_ratio);
                break;
            //规定高|宽, 等比缩放
            case 2:
                $_ratio = max($size[0] / $this->sizeSrc[0], $size[1] / $this->sizeSrc[1]);
                if ($_ratio == 0) break;
                $this->sizeDst[0] = ceil($this->sizeSrc[0] * $_ratio);
                $this->sizeDst[1] = ceil($this->sizeSrc[1] * $_ratio);
                break;
        }
        $image = imagecreatetruecolor($this->sizeDst[0], $this->sizeDst[1]);
        $color = imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 0, 0, $color);
        return $image;
    }

    /**
     * get image resource.(获取图片资源)
     * @param   string $filename 图片路径
     * @return  null|resource
     */
    protected function  &getImageSource($filename) {

        $img = NULL;
        $image = getimagesize($filename);
        $extension = explode('/', $image['mime']);
        switch ( $extension[1] ) {

            case 'gif':
                $img = imagecreatefromgif($filename);
                break;

            case 'png':
                $img = imagecreatefrompng($filename);
                break;

            case 'jpg':
            case 'jpeg':
                $img = imagecreatefromjpeg($filename);

        }
        return $img;
    }

    /**
     * 计算出目标图片的尺寸。
     * 当图片缩放后的大小比画布小时，图片居中，多余的补白
     * @param    float $ratio 缩放比率
     * @return array
     */
    protected function getDstImageSize($ratio) {

        return array(floor($this->sizeSrc[0] * $ratio), floor($this->sizeSrc[1] * $ratio));

    }

    /**
     * 获取图片缩放比率
     * @param  array $size 目标图片画布大小
     * @return mixed
     */
    protected function getZoomRatio($size) {

        return min($size[0] / $this->sizeSrc[0], $size[1] / $this->sizeSrc[1]);
    }

    /**
     * @param int $flag
     */
    public function setFlag($flag)
    {
        $this->flag = $flag;
    }

    /**
     * @return int
     */
    protected function getFlag()
    {
        return $this->flag;
    }

    /**
     * destroy image resource(销毁图片资源，释放内存)
     */
    public function __distruct() {

        if ( $this->imgSrc )
            imagedestroy($this->imgSrc);

        if ( $this->imgDst )
            imagedestroy($this->imgDst);
    }

}
