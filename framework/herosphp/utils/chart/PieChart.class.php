<?php
/**
 * 生成饼状图类, 根据传入的数据生成饼状统计图。<br />
 * The class to make pie chart.
 * *******************************************************************************
 * 许可声明：此为专门为网络星空PHP高性能建站班级量身定制的"轻量级"PHP框架
 * *******************************************************************************
 * 版权所有 (C) 2013.03-now 网络星空工作室研发中心 并保留所有权利。           	
 * @author 	yangjian<yangjian102621@gmail.com>
 * @completed 	2013.04.14
 * @version 	1.0
 */
class PieChart implements IChart {
	
	/* size of canvas(画布大小) */
	private $bg_size = array(600, 500);
	/* pie chart size */
	private $pie_size = array(450, 150);

	/* image resource */
	private $image = NULL;
	/* chart data array */
	private $data = array();
	/* title of chart */
	private $title;

	/* title font */
	private $t_font = 1;
	/* string font */
	private $str_font = 0;
	/* title font size */
	private $t_fsize = 20;
	/* string font size */
	private $str_fsize = 14;

	/* pie layers */
	private $pie_layers = 20;
	/* color increase step (立体图颜色的递增步长) */
	private $color_step = 2;

	/* title space to chart (标题和图表的距离) */
	private $t2c_space = 20;
	/* chart space to note (图表和注解的距离) */
	private $c2n_space = 30;
	/* note 注解行距 */
	private $note_space = 10;
	/* 画布顶部边距 */
	private $margin_top = 10;
	/* 画布底部边距 */
	private $margin_bottom = 40;

	/* title position */
	private $title_pos = array();
	/* pie position */
	private $pie_pos = array();
	/* node position */
	private $note_pos = array();

	/* background color for canvas */
	private $bg_color = array(255, 255, 0);
	/* title color */
	private $t_color = array(180, 0, 0);
	/* string color */
	private $str_color = array(128,0,255);

	private static $_FONT = array(
		0 => 'hanyi.ttf',
		1 => 'hanyi-xiu-ying.ttf'
	);
	private $font_dir = NULL;

	public function __construct( &$_config ) {
		if ( !isset($_config['data']) ) die('chart data should not be empty!');
		$this->data = $_config['data'];
		asort($this->data);
		if ( isset($_config['title']) ) $this->title = $_config['title'];
		if ( isset($_config['bg_width']) ) $this->bg_size[0] = $_config['bg_width'];
		if ( isset($_config['pie_size']) ) $this->pie_size = $_config['pie_size'];
		if ( isset($_config['bg_color']) ) $this->bg_color = $_config['bg_color'];
		if ( isset($_config['t_font']) ) $this->t_font = $_config['t_font'];
		if ( isset($_config['str_font']) ) $this->str_font = $_config['str_font'];
		if ( isset($_config['t_fsize']) ) $this->t_fsize = $_config['t_fsize'];
		if ( isset($_config['str_fsize']) ) $this->str_fsize = $_config['str_fsize'];
		if ( isset($_config['str_color']) ) $this->str_color = $_config['str_color'];
		if ( isset($_config['t_color']) ) $this->t_color = $_config['t_color'];
		if ( isset($_config['pie_layers']) ) $this->pie_layers = $_config['pie_layers'];
		if ( isset($_config['color_step']) ) $this->color_step = $_config['color_step'];
		if ( isset($_config['t2c_space']) ) $this->t2c_space = $_config['t2c_space'];
		if ( isset($_config['c2n_space']) ) $this->c2n_space = $_config['c2n_space'];
		if ( isset($_config['note_space']) ) $this->t2c_space = $_config['note_space'];
		if ( isset($_config['margin_top']) ) $this->margin_top = $_config['margin_top'];
		if ( isset($_config['margin_bottom']) ) $this->margin_top = $_config['margin_bottom'];
		$this->font_dir = ROOT.DIR_OS.'libs'.DIR_OS.'fonts'.DIR_OS;

		$this->initCompPos();
		$this->image = $this->getImageCanvas($this->bg_size, $this->bg_color);
		$this->drawPie();
		$this->drawTitle();
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

	/**
	 * draw pie 绘制$this->pie_layers 层
	 */
	private function drawPie() {
	
		$_color_bak = array();	//保持每个圆饼的颜色, 以确保每次绘制颜色不变
		$_lastlayer_color = NULL;
		for ( $k = 0; $k < $this->pie_layers; $k++  ) {
			$i = 0;
			$_start = 0;	//圆饼的起始位置 
			$_end = 0;		//圆饼的结束为止
			foreach ( $this->data as $_val ) {
				$_start = ($_end == 0) ? 0 : $_end;
				$_end = ($_start == 0) ? ($_val/array_sum($this->data))*360 : ($_val/array_sum($this->data))*360 + $_start;
				$_end = min($_end, 360);

				// put the color to the color_bak[] (将第一层的颜色依次存入备份颜色数组中)
				if ( $k == 0 ) {
					//使用随机颜色
					$_color = array(mt_rand(10, 210),mt_rand(10, 210),mt_rand(10, 210));
					$_color_bak[$i] = $_color;
				}
				imagefilledarc($this->image, $this->pie_pos[0],$this->pie_pos[1]-$k, $this->pie_size[0], $this->pie_size[1], $_start, $_end, $this->getColor($_color_bak[$i], $this->color_step*$k),IMG_ARC_PIE);
				//存储最后一层的颜色，用于做文字标注
				if ( $k == ($this->pie_layers - 1) ) {
					$_lastlayer_color[$i] = array($_color_bak[$i][0]+$this->color_step*$k, $_color_bak[$i][1]+$this->color_step*$k, $_color_bak[$i][2]+$this->color_step*$k);
				}
				$i++;
			}
		}
		//绘制注解
		$this->drawNotes($this->data, $_lastlayer_color);
	}

	/**
	 * draw notes 绘制注解
	 * @param  		array 		$_data
	 * @param  		array 		$_color    (数据对应的颜色)
	 */
	private function drawNotes( &$_data, &$_color ) {
		
		$i = 0;
		$_font = $this->font_dir.self::$_FONT[$this->str_font];
		$_str_color = imagecolorallocate($this->image, $this->str_color[0], $this->str_color[1], $this->str_color[2]);
		$_rect_w = 40;		//方块的宽度
		$_space = 10;		//文字与图形的间距
		foreach ( $_data as $_key => $_val ) {
			$_ttf_box = imagettfbbox($this->str_fsize, 0, $_font, $_key);
			$_step = abs($_ttf_box[7])+$this->note_space;
			$_x = $this->note_pos[0]; 
			$_y = $this->note_pos[1] + floor($i/2) * $_step;
			$_rect_color = imagecolorallocate($this->image, $_color[$i][0], $_color[$i][1], $_color[$i][2]); 
			if ( $i % 2 != 0 ) $_x += $this->pie_size[0] / 2;
			//绘制矩形
			imagefilledrectangle($this->image, $_x, $_y-abs($_ttf_box[7]), $_x+$_rect_w, $_y, $_rect_color);
			//绘制文字
			$x = $_x+$_rect_w+$_space;
			imagettftext($this->image, $this->str_fsize, 0, $x, $_y, $_str_color, $_font, $_key);
			//绘制百分比
			// 算出相应数据的百分比
			$_percent = '['.number_format($_val/array_sum($_data)*100,2,'.','').'%]';
			$_p_x = $x + ($_ttf_box[2] - $_ttf_box[0]);
			imagestring($this->image, $this->str_fsize, $_p_x+$_space, $_y-$this->str_fsize, $_percent, $_str_color);
			$i++;
		}
	}

	/**
 	 * draw title of chart
	 */
	private function drawTitle() {
		$_t_color = imagecolorallocate($this->image, $this->t_color[0], $this->t_color[1], $this->t_color[2]);
		$_font = $this->font_dir.self::$_FONT[$this->t_font];
		imagettftext($this->image, $this->t_fsize, 0, $this->title_pos[0], $this->title_pos[1], $_t_color , $_font, $this->title);
	}

	/**
	 * get color from color array
	 */
	private function &getColor( $_color, $_step ) {
		return imagecolorallocate($this->image, $_color[0]+$_step, $_color[1]+$_step, $_color[2]+$_step);
	}

	/**
	 * initialize compnents position (初始化组件的位置)
	 */
	private function initCompPos() {
		$_tfont = $this->font_dir.self::$_FONT[$this->t_font];
		$_ttf_box = imagettfbbox($this->t_fsize, 0, $_tfont, $this->title);

		$this->title_pos[0] = ($this->bg_size[0] - ($_ttf_box[2] - $_ttf_box[0]))/2;
		$this->title_pos[1] = $this->margin_top + abs($_ttf_box[7]);
	
		$this->pie_pos[0] = $this->bg_size[0] / 2;
		$this->pie_pos[1] = $this->title_pos[1] + $this->t2c_space + $this->pie_size[1] / 2 + $this->pie_layers;

		$this->note_pos[0] = $this->pie_pos[0] - $this->pie_size[0] / 2;
		$this->note_pos[1] = $this->pie_pos[1] + $this->pie_size[1] / 2 + $this->c2n_space;

		//重新计算画布的高度
		$_font = $this->font_dir.self::$_FONT[$this->str_font];
		$_ttf_box = imagettfbbox($this->str_fsize, 0, $_font, "data测试");
		$this->bg_size[1] = $this->pie_pos[1] + $this->pie_size[1] / 2 + ceil(count($this->data)/2) * (abs($_ttf_box[7])+$this->note_space) + $this->margin_bottom;
	}

	/**
	 * create an truecolor canvas(创建真彩色画布)
	 * @param       array 		$_size
	 * @param  		array 		$_bg_color
	 */
	private function &getImageCanvas( $_size, $_bg_color= array(255, 255, 255) ) {
		$_img =  imagecreatetruecolor($_size[0], $_size[1]);
		$_color = imagecolorallocate($_img, $_bg_color[0], $_bg_color[1], $_bg_color[2]);
		imagefill($_img, 0, 0, $_color);
		return $_img;
	}

	public function __destruct() {
		if ( $this->image )
			imagedestroy($this->image);
	}
}
?>