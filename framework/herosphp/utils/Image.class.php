<?php
/**
 * 图片处理类, 实现了对图片的缩放和添加水印, 支持jpg, png, gif格式的图片.
 * 支持图片水印和文字水印， 有7种水印字体可选。
 * Image processing class, resize img and add watermark.
 * *******************************************************************************
 * 许可声明：此为专门为网络星空PHP高性能建站班级量身定制的"轻量级"PHP框架
 * *******************************************************************************
 * 版权所有 (C) 2013.03-now 网络星空工作室研发中心 并保留所有权利。           	
 * @author 	yangjian<yangjian102621@gmail.com>
 * @version  1.2
 * @completed 	2013.04.11
 * @last-update	2013.04.11
 * ******************************************************************************/
class Image {

	/* only instance of Image class  */
	private static $_instance = NULL;

	/* Source image */
	private $img_src = NULL;
	/* Destination image */
	private $img_dst = NULL;
	/* src image size */
	private $size_src = array();
	/* dst image size */
	private $size_dst = array();
	/* position of dst image (目标图片在拷贝画布上的位置) */
	private $dst_pos = array(0, 0);
	/* 缩放方式(默认是直接缩放)*/
	private $flag = 0;

	/* water image */
	private $img_water = NULL;
	/* watermark image position(水印位置默认在右下角) */
	private $water_pos = array();
	/* size of water image */
	private $water_size = array();
	/* water font */
	private $water_font = 4;
	/**
	 * water font map (水印字体映射表)
	 * 想要添加字体的用户只需在libs/fonts 文件夹中添加字体文件，然后在这数组中添加与字体名称
	 */
	private static $FONT_MAP = array(
		'0' 	=>  'suti.ttf', 			//新苏鹅卵石体
		'1' 	=>  'mao-ze-dong.ttf', 		//毛泽东体
		'2' 	=>  'hanyi-xiu-ying.ttf',	//汉仪秀英繁体
		'3' 	=> 	'wending-huocai.ttf', 		//文鼎火柴体 (英文字体)
		'4'		=>  'ruixian-jt.ttf',		//张海山锐线体简
		'5'		=>  'hanyi-zhujie.ttf',		//汉仪竹节繁体
		'6'		=>  'ye-jing.ttf'			//液晶体(英文字体)
	);

	/* switch of debug mode */
	private $debug = false;

	private function __construct() {}

	public static function getInstance() {
		if ( self::$_instance == NULL )
			self::$_instance = new self();
		return self::$_instance;
	}

	/**
	 * make thumb pic from source img (生成缩略图)
	 * @param  		array 		$_size    	new size of image
	 * @param  		string 		$_img_src	Original image file path
	 * @param 		string 		$_img_dst   destnation image file path
	 * @param 		int			$_flag 	缩放方式
	 * 		0 => 直接缩放成目标大小,  
     *      1 => 缩放成目标大小, 但是要保持比例
	 * 		2 => 规定宽度/高度, 其他一方等比缩放,等比缩放的一方设置为0,如: $_size = array(500, 0) 表示
	 * 		限制宽度为500, 高度等比缩放.
	 * @param 		int 		$_quality   图片质量(0-100)
	 * @param  		array 		$_canvas   	画布填充颜色	
	 */
	public function makeThumb( $_size, $_img_src, $_img_dst, $_flag = 1, $_quality = 75, $_canvas = NULL ) {
		$this->flag = $_flag;
		$_thumb_dir = dirname($_img_dst);
		if ( !file_exists($_thumb_dir) ) Utils::makeFileDirs($_thumb_dir);
		if ( $_canvas == NULL ) $_canvas = array(255,255,255);
		$this->size_src = $this->get_image_size($_img_src);
		$this->get_dstimage_size($_size);		//获取目标图像的大小

		//目标图片的填充与拷贝
		$_color = imagecolorallocate($this->img_dst, $_canvas[0], $_canvas[1], $_canvas[2]);
		imagefill($this->img_dst, 0, 0, $_color);
		$this->img_src = $this->get_image_source($_img_src);		//获取图片资源
		if ( $this->img_src && $this->img_dst ) {
			$_res = imagecopyresampled($this->img_dst, $this->img_src, $this->dst_pos[0], $this->dst_pos[1], 0, 0, $this->size_dst[0], $this->size_dst[1], $this->size_src[0], $this->size_src[1]);
			if ( !$_res ) trigger_error("图像拷贝失败 [{$_img_src}]!");
		}
		
		$this->save_image($_img_dst, $this->img_dst, $_quality);	//图像输出,保存
	}

	/**
 	 * sava image 
 	 * @param 		string 		$_filename 保存新的文件名称
 	 * @param 		resource 	$_image  	图片资源
 	 * @param 		int 		$_quality 	图片质量，仅对jpeg图像有效
	 */
	private function save_image( $_filename, $_image, $_quality = 75 ) {
		$_img_ext = Utils::getFileExt( $_filename );
		$_res = true;
		switch ( $_img_ext ) {
			case 'jpg':
			case 'jpeg':
				$_res = imagejpeg($_image, $_filename, $_quality);
				break;
			case 'gif':
				$_res = imagegif($_image, $_filename);
				break;
			case 'png':
				$_res = imagepng($_image, $_filename);
				break;
			default:
				$_res = imagejpeg($_image, $_filename);
		}
		if ( !$_res ) {
			trigger_error("图片[{$_filename}]保存失败, 后缀：{$_img_ext}");
		} elseif ( $this->debug ) {
			header("Content-Type:image/".$_img_ext);
			imagejpeg($_image, NULL, $_quality);
		}
	}	

	/**
	 * 给图片图片水印
	 * @param 		string 		$_img_src  	 	原图片的路径
	 * @param 		string 		$_water_path 	水印图片的路径
	 * @param 		int  		$_water_pos		水印图片的位置
	 * 		0   =>   左上角		1 	=>   右上角
	 * 		2   => 	 右下角 	3 	=> 	 左下角
	 * 		4 	=>   绝对居中	5 	=> 	 随机位置
	 * @param 		int 		$_water_alpha 	水印的透明度(0-100)
	 */
	public function imageWaterMark( $_img_src, $_water_path, $_water_pos, $_water_alpha = NULL ) {
		$this->size_dst = $this->get_image_size($_img_src);
		$_img_ext = Utils::getFileExt($_water_path);

		//创建画布
		$this->img_dst = $this->getImageTrueColor($this->size_dst[0], $this->size_dst[1], $_img_ext);
		$this->img_src = $this->get_image_source($_img_src);

		//将图像载入画布
		imagecopyresampled($this->img_dst, $this->img_src, 0, 0, 0, 0, $this->size_dst[0], 
		                  $this->size_dst[1], $this->size_dst[0], $this->size_dst[1]);

		$this->img_water = $this->get_image_source($_water_path);
		$this->water_size = $this->get_image_size($_water_path);	//获取水印大小
		$this->get_water_pos($this->size_dst, $this->water_size, $_water_pos);	//获取水印位置
		if ( $this->img_dst && $this->img_water ) {
			$_res = imagecopymerge($this->img_dst, $this->img_water, $this->water_pos[0],$this->water_pos[1],
			                          0, 0, $this->water_size[0], $this->water_size[1], $_water_alpha);
			if ( !$_res ) trigger_error("图像拷贝失败 [{$_img_src}]!");
		}
		/*header("Content-Type:image/jpeg");
		imagejpeg($this->img_dst, NULL, 75);*/
		//保存图片, 覆盖原图
		$this->save_image($_img_src, $this->img_dst);
	}

	/**
 	 * 给图片添加文字水印
 	 * @param 		string 		$_img_src  	 	原图片的路径
 	 * @param 		string 		$_water_str 	水印文字
 	 * @param 		int 		$_fontsize 		水印字体大小
 	 * @param 		array()		$_color			水印颜色
 	 * @param 		int 		$_water_pos     水印位置
 	 * @see  method makeThumb();
 	 * @param 		float 		$_angle  		文字水印倾斜
	 */
	public function stringWaterMark( $_img_src, $_water_str, $_fontsize, $_str_color = NULL, $_water_pos = 4, $_angle = 0 ) {
		$this->img_src = $this->get_image_source($_img_src);
		if ( $_str_color == NULL ) $_str_color = array(180, 0, 0);
		$_color = imagecolorallocate($this->img_src, $_str_color[0], $_str_color[1], $_str_color[2]);
		$this->size_src = $this->get_image_size($_img_src);

		$_font = dirname(dirname(__FILE__)).DIR_OS.'fonts'.DIR_OS.self::$FONT_MAP[$this->water_font];
		$_ttf_box = imagettfbbox($_fontsize, $_angle, $_font, $_water_str);
		$_size = array(); 	//文字的尺寸
		$_size[0] = $_ttf_box[2] - $_ttf_box[0];
		$_size[1] = abs($_ttf_box[7]);
		$this->get_water_pos($this->size_src, $_size, $_water_pos);
		if ( $this->img_src ) {
			$_res = imagettftext($this->img_src, $_fontsize, $_angle, $this->water_pos[0], 
			                     $this->water_pos[1], $_color, $_font, $_water_str);
			if ( !$_res ) trigger_error("写入文字水印失败, [{$_img_src}]!");
		}
		$this->save_image($_img_src, $this->img_src);		//保存图片，覆盖原图

	}

	/* 设置水印字体 */
	public function setWaterFontStyle( $_font_index ) {
		$this->water_font = intval($_font_index);
	}

	/* open debug mode */
	public function setDebug( $_debug ) {
		$this->debug = (boolean) $_debug;
	}

	/**
	 * get water postion (获取图片水印的位置)
	 * @param  		array 		$_img_size   	目标图片的大小 
	 * @param  		array 		$_water_size    水印图片的大小 
	 * @param  		int 		$_pos_flag   水印位置, 默认在右下角
	 */
	private function get_water_pos( &$_img_size, &$_water_size, $_pos_flag = 2 ) {
		
		switch ( $_pos_flag ) {
			case 0 :	//左上角
				$this->water_pos[0] = 10;
				$this->water_pos[1] = 10;
				break;

			case 1 :	//右上角
				$this->water_pos[0] = ($_img_size[0] - $_water_size[0]) - 10;
				$this->water_pos[1] = 10;
				break;

			case 2 :	//右下角
				$this->water_pos[0] = ($_img_size[0] - $_water_size[0]) - 10;
				$this->water_pos[1] = ($_img_size[1] - $_water_size[1]) - 10;
				break;

			case 3 :	//左下角
				$this->water_pos[0] = 10;
				$this->water_pos[1] = ($_img_size[1] - $_water_size[1]) - 10;
				break;

			case 4 : 	//居中
				$this->water_pos[0] = (int) ($_img_size[0] - $_water_size[0])/2;
				$this->water_pos[1] = (int) ($_img_size[1] - $_water_size[1])/2;
				break;

			case 5 :	//随机
				$this->water_pos[0] = mt_rand(10, ($_img_size[0] - $_water_size[0]) - 10);
				$this->water_pos[1] = mt_rand(10, ($_img_size[1] - $_water_size[1]) - 10);
				break; 
		}
	}

	/* initialization image info */
	private function &get_image_size( $filename ) {
		$_size = array();
		$_info = getimagesize($filename);
		$_size[0] = $_info[0];
		$_size[1] = $_info[1];
		return $_size;
	}

	/* get destination image size  */
	private function get_dstimage_size( $_size ) {
		switch ( $this->flag ) {
			//直接缩放
			case 0:
				$this->size_dst = $_size;
				$this->img_dst = imagecreatetruecolor($_size[0], $_size[1]);
				break;
			//等比缩放到指定size
			case 1:
				$this->img_dst = imagecreatetruecolor($_size[0], $_size[1]);
				$_ratio = $this->get_zoom_ratio($_size);	//获取缩放比例
				if ( $_ratio == 0 ) return;
				$this->get_dstimage_pos($_ratio, $_size);
				break;
			//规定高|宽, 等比缩放
			case 2:
				$_ratio = max($_size[0]/$this->size_src[0], $_size[1]/$this->size_src[1]);	
				if ( $_ratio == 0 ) return;
				$this->size_dst[0] = intval( $this->size_src[0] * $_ratio );
				$this->size_dst[1] = intval( $this->size_src[1] * $_ratio );
				$this->img_dst = imagecreatetruecolor($this->size_dst[0], $this->size_dst[1]);
				break;
		}
	}

	/**
 	 * get image resource.(获取图片资源)
 	 * @param 		string 		$_filename   图片路径
	 */
	private function  &get_image_source( $_filename ) {
		$_img = NULL;
		$_img_ext = Utils::getFileExt( $_filename );
		switch ( $_img_ext ) {
			case 'jpg':
			case 'jpeg':
				$_img = imagecreatefromjpeg($_filename);
				break;
			case 'gif':
				$_img = imagecreatefromgif($_filename);
				break;
			case 'png':
				$_img = imagecreatefrompng($_filename);
				break;
			default:
				$_img = imagecreatefromjpeg($_filename);
		}
		return $_img;
	}

	/**
	 * 创建真彩色画布
	 * @param 		int 	$_width 	 画布宽度
	 * @param 		int 	$_height  	 画布高度
	 * @param 		string 	$_flag 		 画布参数
	 * @return      image resource       返回图片资源
	 */
	private function getImageTrueColor( $_width, $_height, $_flag ) {
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
	 * 获取目标图片在画布中的偏移位置。
	 * 当图片缩放后的大小比画布小时，图片居中，多余的补白
	 * @param   	float 		$_ratio  	缩放比率
	 * @param  		array 		$_size  	画布大小数组
	 */
	private function get_dstimage_pos( $_ratio, $_size ) {
		$this->size_dst[0] = floor($this->size_src[0] * $_ratio);
		$this->size_dst[1] = floor($this->size_src[1] * $_ratio);
		$this->dst_pos[0] = intval( ($_size[0] - $this->size_dst[0])/2 );
		$this->dst_pos[1] = intval( ($_size[1] - $this->size_dst[1])/2 );

	}

	/**
	 * Get the zoom ratio(获取缩放比率)
	 * @param 		array 		$_size 		画布大小
	 */
	private function get_zoom_ratio( $_size ) {
		return min($_size[0]/$this->size_src[0], $_size[1]/$this->size_src[1]);
	}

	/**
	 * destroy image resource(销毁图片资源，释放内存) 
	 */
	public function __distruct() {
		imagedestroy($this->img_src);
		imagedestroy($this->img_dst);
		imagedestroy($tis->img_water);
	}
	
} 
?>