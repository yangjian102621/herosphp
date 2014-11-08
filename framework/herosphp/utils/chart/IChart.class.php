<?php
/**
 * 统计图生成类统一接口。<br />
 * interface for chart make class.
 * *******************************************************************************
 * 许可声明：此为专门为网络星空PHP高性能建站班级量身定制的"轻量级"PHP框架
 * *******************************************************************************
 * 版权所有 (C) 2013.03-now 网络星空工作室研发中心 并保留所有权利。           	
 * @author 	yangjian<yangjian102621@gmail.com>
 * @completed 	2013.04.14
 * @version 	1.0
 */
interface IChart {

	/**
	 * show chart in browser
	 */
	public function showChart();

	/**
  	 * set save chart to file
  	 * @param     string 		$_filename    file for the chart.
  	 * @param     string        $_ext		  extension of image
  	 * @param  	  int  			$_quality  	  quality of image(only for jpeg image)
	 */
	public function saveChart( $_filename, $_ext, $_quality = 75 ); 

}
?>