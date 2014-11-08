<?php
/**
 * 生成柱形统计图<br />
 * class to draw square chart.
 * *******************************************************************************
 * 许可声明：此为专门为网络星空PHP高性能建站班级量身定制的"轻量级"PHP框架
 * *******************************************************************************
 * 版权所有 (C) 2013.03-now 网络星空工作室研发中心 并保留所有权利。           	
 * @author 	yangjian<yangjian102621@gmail.com>
 * @completed 	2013.04.14
 * @version 	1.0
 */
class SquareChart implements IChart {

	/* image resource */
	private $image = NULL;
	/* canvas size of chart (画布大小) */
	private $bg_size = array(400, 500);
	/* size of Column chart 方块的大小 */
	private $square_size = array(40, 20);
	/* angle of Polygon */
	private $p_angle = 45;
	/* y轴标点 步长 => 点数 */
	private $axisy = array(100, 10);
	/* measure of data (计数单位) */
	private $measure = '名';
	/* 英文字体大小 */
	private $en_fsize = 3;
	/* 坐标轴标尺长度 */
	private $staff_width = 8;

	/* data of chart */
	private $data = NULL;
	private $title;
	/**
	 * $t_chart 		int    	(图像类型)
	 * 0 => 2D条形图	1 => 3D 条形图
	 * 2 => 3D 柱形图 
	 */
	private $t_chart = 1;

	/* color of canvas */
	private $bg_color = array(255,255,255);
	/* color of title */
	private $t_color = array(180, 0, 0);
	/* color of string */
	private $str_color = array(0, 64, 128);
	/* color of axis */
	private $axis_color = array(128, 64, 64);

	/* 画布左边距 */
	private $margin_left = 50;
	/* 画布右边距 */
	private $margin_right = 20;
	/* 画布上边距 */
	private $margin_top = 10;
	/* 画布下边距 */
	private	$margin_bottom = 50;
	/* Y轴距离顶部的距离 */
	private $axisY_top = 50;
	/* title space to chart (标题和图表的距离) */
	private $t2c_space = 20;

	private $t_fsize = 20;
	private $str_fsize = 12;

	private $t_font = 0;
	private $str_font = 0;
	private static $_FONT = array(
		0 => 'hanyi.ttf',
		1 => 'hanyi-xiu-ying.ttf'
	);
	private $font_dir = NULL;

	public function __construct( &$_config ) {
		if ( !isset($_config['data']) ) die('chart data should not be empty!');
		$this->data = $_config['data'];
		asort($this->data);
		if ( isset($_config['axisy']) ) $this->axisy = $_config['axisy'];
		if ( isset($_config['title']) ) $this->title = $_config['title'];
		if ( isset($_config['bg_size']) ) $this->bg_size = $_config['bg_size'];
		if ( isset($_config['margin_left']) ) $this->margin_left = $_config['margin_left'];
		if ( isset($_config['margin_right']) ) $this->margin_right = $_config['margin_right'];
		if ( isset($_config['margin_top']) ) $this->margin_left = $_config['margin_top'];
		if ( isset($_config['margin_bottom']) ) $this->margin_bottom = $_config['margin_bottom'];
		if ( isset($_config['bg_color']) ) $this->bg_color = $_config['bg_color'];
		if ( isset($_config['str_color']) ) $this->str_color = $_config['str_color'];
		if ( isset($_config['t_color']) ) $this->t_color = $_config['t_color'];
		if ( isset($_config['t_fsize']) ) $this->t_fsize = $_config['t_fsize'];
		if ( isset($_config['str_fsize']) ) $this->str_fsize = $_config['str_fsize'];
		if ( isset($_config['square_size']) ) $this->square_size = $_config['square_size'];
		if ( isset($_config['p_angle']) ) $this->p_angle = $_config['p_angle'];
		if ( isset($_config['t_font']) ) $this->t_font = $_config['t_font'];
		if ( isset($_config['str_font']) ) $this->str_font = $_config['str_font'];
		if ( isset($_config['staff_width']) ) $this->staff_width = $_config['staff_width'];
		if ( isset($_config['axis_color']) ) $this->axis_color = $_config['axis_color'];
		if ( isset($_config['en_fsize']) ) $this->en_fsize = $_config['en_fsize'];
		if ( isset($_config['t2c_space']) ) $this->t2c_space = $_config['t2c_space'];
		if ( isset($_config['measure']) ) $this->measure = $_config['measure'];
		if ( isset($_config['t_chart']) ) $this->t_chart = $_config['t_chart'];

		$this->font_dir = ROOT.DIR_OS.'libs'.DIR_OS.'fonts'.DIR_OS;

		$this->image = $this->getImageCanvas($this->bg_size, $this->bg_color);
		$this->drawTitle();
		$this->drawSquare();
	}

	/**
	 * create an truecolor canvas(创建真彩色画布)
	 * @param       array 		$_size
	 * @param  		array 		$_bg_color
	 */
	private function &getImageCanvas( $_size, $_bg_color = array(255, 255, 255) ) {
		$_img =  imagecreatetruecolor($_size[0], $_size[1]);
		$_color = imagecolorallocate($_img, $_bg_color[0], $_bg_color[1], $_bg_color[2]);
		imagefill($_img, 0, 0, $_color);
		return $_img;
	}

	/**
	 * draw square chart 绘制柱形图
	 */
	private function drawSquare() {
		//坐标轴原点
		$x0 = $this->margin_left;
		$y0 = $this->bg_size[1] - $this->margin_bottom;
		//坐标轴终端
		$y_top = $this->axisY_top ;
		$x_right = $this->bg_size[0] - $this->margin_right;
		$_space = 10;

		$_color_axis = imagecolorallocate($this->image, $this->axis_color[0], $this->axis_color[2], $this->axis_color[2]);
		$_color_str = imagecolorallocate($this->image, $this->str_color[0], $this->str_color[1], $this->str_color[2]);
		$_font = $this->font_dir.self::$_FONT[$this->str_font];

		// 绘制Y坐标轴
		imageline($this->image, $x0, $y0, $x0, $y_top, $_color_axis);
		
		// 绘制X坐标轴
		imageline($this->image, $x0, $y0, $x_right, $y0, $_color_axis);

		$_step_x = intval( ($x_right - $x0 - $this->square_size[0] ) /count($this->data) );
		$_step_y = intval( ($y0 - $y_top ) /$this->axisy[1] );		//坐标步长
		$_axis_h = $y0 - $y_top;			//Y坐标轴的高度
		$_axis_max = $this->axisy[0] * $this->axisy[1];		//Y轴的最大值

		//给Y轴标点
		for ( $j = 1; $j <= $this->axisy[1]; ++$j ) {
			imageline($this->image, $x0,  $y0 - $_step_y * $j, $x0 - $this->staff_width, $y0 - $_step_y * $j, $_color_axis);
			//绘制数值
			imagestring ( $this->image , $this->en_fsize , $x0+$_space , $y0 - $_step_y * $j - imagefontheight($this->en_fsize)/2 , $this->axisy[0]*$j , $_color_str );
		}
		$i = 1;
		foreach ( $this->data as $_key => $_val ) {
			//绘制标尺
			imageline($this->image, $x0 + $_step_x * $i, $y0 - $this->staff_width, $x0 + $_step_x*$i, $y0, $_color_axis);
			//绘制柱形图
			$_x1 = $x0 + $_step_x * $i - $this->square_size[0]/2;
			$_y1 = $y0;
			$_height = intval($_axis_h * ($_val/$_axis_max));
			switch ( $this->t_chart ) {
				case 0:
					$this->draw2DBarChart($_x1, $_y1, $_height);
					break;
				case 1:
					$_x1 += $this->square_size[1] * cos($this->p_angle);
					$this->draw3DBarChart($_x1, $_y1, $_height);
					break;
				case 2:
					$this->draw3DPieChart($_x1, $_y1, $_height);
					break;
			}

			//绘制文字
			//$_color = imagecolorallocate($this->image, $this->t_color[0], $this->t_color[1], $this->t_color[2]);
			imagettftext($this->image, $this->str_fsize, 0, $_x1, $_y1-$_height-$this->square_size[1], $_color_str, $_font, $_val.$this->measure);

			//绘制横坐标标度
			$_ttf_box = imagettfbbox($this->str_fsize, 0, $_font, $_key);
			$_x = $x0 + $_step_x * $i - ($_ttf_box[2] - $_ttf_box[0])/2;
			$_y = $y0 + abs($_ttf_box[7]) + ($this->t_chart == 2 ? 2*$_space : $_space);
			imagettftext($this->image, $this->str_fsize, 0, $_x, $_y, $_color_str, $_font, $_key);
			++$i;
		}
			
	}

	/**
 	 * 绘制3D条形图
 	 * @param 	   	int 		$_x
 	 * @param 		int 		$_y
 	 * @param   	int 		$_height 		条形图的高度
	 */
	private function draw3DBarChart( $_x, $_y, $_height ) {
		//随机取色
		$_step = 20;
		$_c = array( mt_rand(5+$_step, 240-$_step), mt_rand(5+$_step, 240-$_step), mt_rand(5+$_step, 240-$_step) );
		$_color = imagecolorallocate($this->image, $_c[0], $_c[1], $_c[2]);		//中间色
		$_color_dark = imagecolorallocate($this->image, $_c[0] - $_step, $_c[1] - $_step, $_c[2] - $_step);
		$_color_light = imagecolorallocate($this->image, $_c[0] + $_step, $_c[1] + $_step, $_c[2] + $_step);
		$_points = NULL;
		$_y -= $this->square_size[1] * sin($this->p_angle);
		$p1 = array($_x, $_y);
		$p2 = array($_x + $this->square_size[0], $_y);
		$p3 = array($_x + $this->square_size[0] - $this->square_size[1] * cos($this->p_angle), $_y + $this->square_size[1] * sin($this->p_angle));
		$p4 = array($_x - $this->square_size[1] * cos($this->p_angle), $_y + $this->square_size[1] * sin($this->p_angle));
		for ( $i = 0; $i < $_height ; ++$i ) {
			$_points = array(
				$p1[0], $p1[1] - $i, 	//p1
				$p2[0], $p2[1] - $i, 	//p2	
				$p3[0], $p3[1] - $i,	//p3
				$p4[0], $p4[1] - $i	//p4
			);
			if ( $i == ($_height - 1) ) {
				//绘制俯视图
				imagefilledpolygon ( $this->image , $_points , 4 , $_color_light );
			} else {
				//绘制右视图
				imagefilledpolygon ( $this->image , $_points , 4 , $_color_dark );
				//绘制主视图
				if ( $this->p_angle > 45 && $this->p_angle < 80 ) {
					imageline($this->image, $p1[0], $p1[1] - $i, $p2[0], $p2[1] - $i, $_color);
				} else if( $this->p_angle <= 45 && $this->p_angle > 10 ) {
					imageline($this->image, $p3[0], $p3[1] - $i, $p4[0], $p4[1] - $i, $_color);
				}
			}
		}
	}

	/**
	 * 绘制2D条形图
	 * @param 		int 		$_x
	 * @param 		int 		$_y
	 * @param 		int 		$_height  (条形图的高度)
	 */
	private function draw2DBarChart( $_x, $_y, $_height ) {
		$_x1 = $_x + $this->square_size[0];
		$_y1 = $_y - $_height;
		$_color = imagecolorallocate($this->image, mt_rand(20, 230), mt_rand(20, 230), mt_rand(20, 230));
		imagefilledrectangle($this->image, $_x, $_y, $_x1, $_y1, $_color);
	}

	/**
	 * 绘制3D柱形图
	 * @param 		int 		$_x
	 * @param 		int 		$_y
	 * @param 		int 		$_height  (条形图的高度)
	 */
	private function draw3DPieChart( $_x, $_y, $_height ) {
		$_x += $this->square_size[0]/2;
		//$_y -= $this->square_size[1]/2;
		//随机取色
		$_step = 15;
		$_c = array( mt_rand(5+$_step, 240-$_step), mt_rand(5+$_step, 240-$_step), mt_rand(5+$_step, 240-$_step) );
		$_color = imagecolorallocate($this->image, $_c[0], $_c[1], $_c[2]);		//中间色
		$_color_dark = imagecolorallocate($this->image, $_c[0] - $_step, $_c[1] - $_step, $_c[2] - $_step);
		$_color_light = imagecolorallocate($this->image, $_c[0] + $_step, $_c[1] + $_step, $_c[2] + $_step);
		
		for ( $i = 0; $i < $_height ; ++$i ) {
			if ( $i == ($_height - 1) ) {
				//绘制俯视图
				imagefilledarc($this->image, $_x, $_y-$i, $this->square_size[0], $this->square_size[1], 0, 360 , $_color_light, IMG_ARC_PIE);
			} else {
				//从0度到45度画深色(绘制右视图)
				imagefilledarc($this->image, $_x, $_y-$i, $this->square_size[0], $this->square_size[1], 0, 60 , $_color_dark, IMG_ARC_PIE);
				//从45到180度画主视图色(绘制主视图)
				imagefilledarc($this->image, $_x, $_y-$i, $this->square_size[0], $this->square_size[1], 60, 180 , $_color, IMG_ARC_PIE);
			}
		}
	}

	/* draw title  of chart */
	private function drawTitle() {
		$_color = imagecolorallocate($this->image, $this->t_color[0], $this->t_color[1], $this->t_color[2]);
		$_font = $this->font_dir.self::$_FONT[$this->t_font];
		$_ttf_box = imagettfbbox($this->t_fsize, 0, $_font, $this->title );
		$_x = ($this->bg_size[0] - ($_ttf_box[2] - $_ttf_box[0]))/2;
		$_y = $this->margin_top + abs($_ttf_box[7]);
		imagettftext($this->image, $this->t_fsize, 0, $_x, $_y, $_color, $_font, $this->title);
		$this->axisY_top = $_y + $this->t2c_space;
	}

	/**
	 * show chart in browser
	 */
	public function showChart() {
		if ( $this->image ) {
			header('Content-Type: image/jpeg');
			imagejpeg($this->image);
		}
		
	}

	/**
  	 * set save chart to file
  	 * @param     string 		$_filename    file for the chart.
  	 * @param     string        $_ext		  extension of image
  	 * @param  	  int  			$_quality  	  quality of image(only for jpeg image)
  	 * @return 	  boolean    
	 */
	public function saveChart( $_filename, $_ext, $_quality = 75 ) {
		$_ext = strtolower($_ext);
		$_res = false;
		if ( $this->image ) {
			switch ( $_ext ) {
				case 'jpg':
					$_res = imagejpeg($this->image, $_filename, $_quality);
					break;
				
				case 'gif':
					$_res = imagegif($this->image, $_filename);
					break;

				case 'png':
					$_res = imagepng($this->image, $_filename);
					break;
			}
		}
		return $_res;
	}
	
	public function __destruct() {
		if ( $this->image ) {
			imagedestroy($this->image);
		}
	}

}
?>