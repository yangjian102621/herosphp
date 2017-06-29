<?php
session_start();
$_root_url = $_SESSION["root_url"];
$_pid = intval($_GET['pid']);
$_data = array(
	array('title' => '图片一', 'url' => 'uploadfiles/image/article/2013/04/1366178375-21-4965.jpg', 'type'=>'image'),
	array('title' => '图片二', 'url' => 'uploadfiles/image/article/2013/04/1366178375-29-7852.zip', 'type'=>'file'),
	array('title' => '图片三', 'url' => 'uploadfiles/image/article/2013/04/1366178375-40-3531.jpg', 'type'=>'image'),
	array('title' => '图片四', 'url' => 'uploadfiles/image/article/2013/04/1366178375-46-3438.css', 'type'=>'file'),
	array('title' => '图片五', 'url' => 'uploadfiles/image/article/2013/04/1366178375-46-3438.jpg', 'type'=>'image'),
	array('title' => '图片六', 'url' => 'uploadfiles/image/article/2013/04/1366178375-64-3669.exe', 'type'=>'file'),
	array('title' => '图片七', 'url' => 'uploadfiles/image/article/2013/04/1366178375-65-8867.jpg', 'type'=>'image'),
	array('title' => '图片八', 'url' => 'uploadfiles/image/article/2013/04/1366178375-65-8867.html', 'type'=>'file'),
	array('title' => '图片五', 'url' => 'uploadfiles/image/article/2013/04/1366178375-53-6373.jpg', 'type'=>'image'),
	array('title' => '图片六', 'url' => 'uploadfiles/image/article/2013/04/1366178375-64-3669.exe', 'type'=>'file'),
	array('title' => '图片七', 'url' => 'uploadfiles/image/article/2013/04/1366178375-64-3669.jpg', 'type'=>'image'),
	array('title' => '图片八', 'url' => 'uploadfiles/image/article/2013/04/1366178375-64-3669.html', 'type'=>'file')
);

function getImageIcon( $_url, $_type ) {
	global $_root_url;
	if ( $_type == 'image' ) {
		return $_root_url.$_url;	
	} else {
		echo $_root_url.'themes/filetypes/icon_'.pathinfo($_url, PATHINFO_EXTENSION).'.gif';	
	}
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>文章所有附件管理</title>

<style>
* {margin:0; padding:0; font-size:12px;}
#div_wrap {text-align:center; margin:auto; overflow:auto; height:380px;}
#opt_menu {text-align:right; height:30px; line-height:30px; background:#e9e9e9;}
#opt_menu a {color:#555555; text-decoration:none; margin-right:10px;}
#opt_menu a:hover {color:#004080; text-decoration:underline;}
.file_list_box {zoom:1; overflow:hidden; margin:auto; padding:5px 0px;}
.image_li {float:left; width:100px; list-style:none;
height:100px; padding:5px 10px; *padding:5px 6px; }
img{border:1px solid #FFF; padding:2px;}
.img_selected { border:1px solid #B40000;}
</style>
</head>

<body>
<div id="div_wrap">
	<!--操作菜单-->
	<div id="opt_menu">
    	<a href="#" onclick="Images.selectAll();">全选</a>
        <a href="#" onclick="Images.deleteMult();">删除选中</a>
    </div>
    
	<ul class="file_list_box">
    	<?php
        foreach ( $_data as $_val ) {
		?>
    	<li class="image_li"><img title="<?=$_val['title']?>" alt="<?=$_val['title']?>" src="<?=getImageIcon($_val['url'], $_val['type']);?>" width="100" height="100" onclick="Images.selectOne(this);" selected="no" node-data="<?=$_root_url.$_val['url']?>" node-type="<?=$_val['type']?>" id="<?=$_val['id']?>" /></li>
        <?php
		}
		?>
    </ul>
</div>
</body>
<script language="javascript">
var Images = {
	selected : false,
	
	//选中全部
	selectAll : function () {
		var imgs = document.getElementsByTagName('img');
		var className = '';
		var selected = '';
		for ( var i = 0; i < imgs.length; i++ ) {
			className = this.selected ? '' : 'img_selected';
			selected = this.selected ? 'no' : 'yes';
			imgs.item(i).className = className;
			imgs.item(i).setAttribute('selected', selected);
			
		}
		this.selected = ! this.selected;	//取反
	},
	
	//选中单个
	selectOne : function( o ) {
		var checked = o.getAttribute('selected');
		var className = checked == 'yes' ? '' : 'img_selected';
		o.className = className;
		var selected = checked == 'yes' ? 'no' : 'yes';
		o.setAttribute('selected', selected);
	},
	
	//批量删除
	deleteMult : function () {
		if ( !window.confirm('您真的要删除所选文件吗？') ) return;
	}	
};

function getImageData() {
	var data = {
		type : [],
		url : [],
		title : []	
	};
	var imgs = document.getElementsByTagName('img');
	var selected;
	for ( var i = 0; i < imgs.length; i++ ) {
		selected = imgs.item(i).getAttribute('selected');
		if ( selected == 'yes' ) {
			data.url.push(imgs.item(i).getAttribute('node-data'));
			data.type.push(imgs.item(i).getAttribute('node-type'));
			data.title.push(imgs.item(i).getAttribute('title'));
		}
	}
	if ( data.url.length == 0 ) {
		window.alert('您未选中任何文件！'); 
		return;	
	}
	return data;
}
</script>
</html>
