<?php
/*---------------------------------------------------------------------
 * 模板编译类。将数据模型导入到模板并输出。
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
 *-----------------------------------------------------------------------*/

namespace herosphp\core;

use herosphp\core\WebApplication;

class Template {
    /**
     * 通过assign函数传入的变量临时存放数组
     * @var array
     */
    private $templateVar = array();

    /**
     * 模板目录
     * @var string
     */
    private $templateDir = "";

    /**
     * 编译目录
     * @var string
     */
    private $compileDir = "";

    /**
     * 模板编译缓存配置
     * 0 : 不启用缓存，每次请求都重新编译(建议开发阶段启用)
     * 1 : 开启部分缓存， 如果模板文件有修改的话则放弃缓存，重新编译(建议测试阶段启用)
     * -1 : 不管模板有没有修改都不重新编译，节省模板修改时间判断，性能较高(建议正式部署阶段开启)
     * @var int
     */
    private $cache = 0;

    /**
     * 模板编译规则
     * @var array
     */
    private static $tempRules = array(
        /**
         * 输出变量,数组
         * {$varname}, {$array['key']}
         */
        '/{\$([^\}]{1,})}/i' => '<?php echo \$${1}?>',
        /**
         * 以 {$array.key} 形式输出一维数组元素
         */
        '/{\$([0-9a-z_]{1,})\.([0-9a-z_]{1,})}/i'	=> '<?php echo \$${1}[\'${2}\']?>',
        /**
         * 以 {$array.key1.key2} 形式输出二维数组
         */
        '/{\$([0-9a-z_]{1,})\.([0-9a-z_]{1,})\.([0-9a-z_]{1,})}/i'	=> '<?php echo \$${1}[\'${2}\'][\'${3}\']?>',

        /**
         * foreach key => value 形式循环输出
         * foreach ( $array as $key => $value )
         */
        '/{loop\s+\$([^\}]{1,})\s+\$([^\}]{1,})\s+\$([^\}]{1,})\s*}/i'   => '<?php foreach ( \$${1} as \$${2} => \$${3} ) { ?>',
        '/{\/loop}/i'    => '<?php } ?>',

        /**
         * foreach 输出
         * foreach ( $array as $value )
         */
        '/{loop\s+\$(.*?)\s+\$([0-9a-z_]{1,})\s*}/i'	=> '<?php foreach ( \$${1} as \$${2} ) { ?>',
		'/{\/loop}/i'	=> '<?php } ?>',

        /**
         * {run}标签： 执行php表达式
         * {expr}标签：输出php表达式
         */
        '/{run\s+(.*?)}/i'   => '<?php ${1} ?>',
        '/{expr\s+(.*?)}/i'   => '<?php echo ${1} ?>',

        /**
         * if语句标签
         * if () {} elseif {}
         */
        '/{if\s+(.*?)}/i'   => '<?php if ( ${1} ) { ?>',
        '/{else}/i'   => '<?php } else { ?>',
        '/{elseif\s+(.*?)}/i'   => '<?php } elseif ( ${1} ) { ?>',
        '/{\/if}/i'    => '<?php } ?>',

        /**
         * 导入模板
         * require|include
         */
        '/{(require|include)\s{1,}([0-9a-z_\.\:]{1,})\s*}/i'
							=> '<?php include $this->getIncludePath(\'${2}\')?>',

        /**
         * 引入静态资源 css file,javascript file
         */
        '/{([res|gres]):([a-z]{1,})\s{1,}([0-9a-z_\.\:\-\/]{1,})\s*}/i'
							=> '<?php echo $this->importResource(\'${1}\', \'${2}\', \'${3}\')?>'
	);

    /**
     * 模板编译配置
     * @var array
     */
    private $configs = array();

    /**
     * 静态资源模板
     * @var array
     */
    private static $resTemplate = array(
		'css'	=> "<link rel=\"stylesheet\" type=\"text/css\" href=\"{url}\" />\n",
		'js'	=> "<script charset=\"utf-8\" type=\"text/javascript\" src=\"{url}\"></script>\n"
	);

	/**
	 * 构造函数
	 */
	public function __construct() {

        $webApp = WebApplication::getInstance();
        $this->configs = $webApp->getConfigs();
        $this->cache = $this->configs['temp_cache'];
        //添加用户自定义的模板编译规则
        $this->addRules($this->configs['temp_rules']);

        //初始化模板目录和编译目录
        $request = $webApp->getHttpRequest();
        $this->configs['module'] = $request->getModule();
        $this->templateDir = APP_PATH.APP_NAME.'/'.$this->configs['module'].'/template/'.$this->configs['template'].'/';
        $this->compileDir = APP_RUNTIME_PATH.'views/'.APP_NAME.'/'.$this->configs['module'].'/';

	}
	
	/**
	 * 增加模板替换规则
     * @param array $rules
	 */
	public  function addRules( $rules ) {
        if ( is_array($rules) && !empty($rules) )
		    self::$tempRules = array_merge(self::$tempRules, $rules);
	}

    /**
     * 将变量分配到模板
     * @param       $var_name
     * @param       string $value 变量值
     */
	public function assign( $var_name, $value ) {
		$this->templateVar[$var_name] = $value;
	}
	
	/**
	 * 获取模板变量
	 * @param		string		$_var_name 变量名 
	 */
	public function getTemplateVar( $_var_name ) {
		return $this->templateVar[$_var_name];
	}

	/**
	 * 编译模板
	 * @param 		string 		$_tempFile 	 	模板文件路径
	 * @param		string		$_compileFile	编译文件路径
	 */
	private function complieTemplate( $_tempFile, $_compileFile ) {
		//获取模板文件
		$_tempContent = @file_get_contents($_tempFile);
		if ( $_tempContent == FALSE ) {
			Debug::appendMessage("加载模板文件 {".$_tempFile."} 失败！请在相应的目录建立模板文件。");
			//写入错误日志
			trigger_error("加载模板文件 {".$_tempFile."} 失败！请在相应的目录建立模板文件。");
		}
		//合并用户和系统的模板替换规则
		$rules = array_merge($this->sysRules, $this->userRules);
		//替换模板
		$_tempContent = preg_replace(array_keys($rules), $rules, $_tempContent);
		
		//complie components
		//$this->complieComponents($_tempContent);
		
		//生成编译目录
		if ( !file_exists(dirname($_compileFile)) ) 
			Utils::makeFileDirs(dirname($_compileFile));
		//生成php文件
		if ( !file_put_contents($_compileFile, $_tempContent, LOCK_EX) ) {
			//生成调试信息
			Debug::appendMessage("生成编译文件 {".$_compileFile."} 失败。");
			//写入错误日志
			trigger_error("生成编译文件 {".$_compileFile."} 失败。");
		}

	}

    /**
     * compile the components
     * @param        string $_html template content.
     * @internal param string $_temp template content after complied.
     */
	private function complieComponents( &$_html ) {
		
		$_pattern = "/".self::$compBegin."(.*?)".self::$compEnd."/is";
		$_m = preg_match_all($_pattern, $_html, $_matches);
		
		if ( $_m != FALSE ) {
			$i = 0;
			foreach ( $_matches[1] as $_val ) {
				$_args = explode(':', $_val);
				$_var = array();
				foreach ( $_args as $_v ) {
					$this->getArgs($_var, $_v);
				}
				
				if ( !isset($_var['name']) || $_var['name'] == '' ) {
					echo '非法的组件名称！';
					continue;
				}
				$_comp = CompFactory::createComp($_var['name']);
				$_script = $_comp->getCompScript($_var);
				$_html = str_replace($_matches[0][$i], $_script, $_html);
				$i++;
			}
		}
		
	} 
	
	/**
	 * 显示模板
	 * @param		string		$_tpl_file		模板文件路径
	 * @param 		string 		$_app_path 		应用程序主目录/也即要显示那主应用下的模板
	 */
	public function display( $_tpl_file=NULL, $_app_path = NULL ) {
		
		//如果没有传入模板文件，则访问默认模块下的默认模板
		if ( $_tpl_file == NULL ) $_tpl_file = HttpRequest::$_request['action'].'_'.HttpRequest::$_request['method'].'.html';
		if ( !$_tpl_file ) $_tpl_file = SysCfg::$dft_action.'_'.SysCfg::$dft_method.'.html';
		
		if ( strpos($_tpl_file, ".html") === FALSE ) $_tpl_file .= '.html';
		
		$this->fileName = $_tpl_file;
		$_compile_file = $this->get_compile_file();
		$_temp_file = $this->get_tpl_file();
		
		if ( file_exists($_temp_file) ) {
			//查看编译文件是否存在或者模板文件是否修改
			if ( !file_exists($_compile_file) 
			     || (!$this->cache && filemtime($_compile_file) < filemtime($_temp_file))
				 || $this->cache == -1 ) {
				$this->complieTemplate($_temp_file, $_compile_file);
			} 
			extract($this->templateVar);	//分配变量
			include $_compile_file;		//包含编译生成的文件
		} else {
			Debug::appendMessage("要编译的模板[{$_temp_file}] 不存在！");	//调试输出
			trigger_error("要编译的模板[{$_temp_file}] 不存在！");	//写入错误日志
		}
		
	}

	/**
 	 * 获取当前模板文件路径
 	 * @return  string 		返回模板文件路径
	 */
	private function get_tpl_file( $filename = NULL ) {
		if ( is_null($filename) ) $filename = $this->fileName;
		//获取当前模板文件目录
		$_tpl_dir = $this->templateDir;
		if ( strpos("../", $filename) === FALSE ) {
			$_path = explode("/", $filename);
			foreach ( $_path as $_file ) {
				if ( $_file == ".." ) {
					$_tpl_dir = dirname($_tpl_dir);
				}
			}
			$filename = str_replace("../", "", $filename);
		}
		$filename = str_replace("/", DIR_OS, $filename);
		$_tpl_file = $_tpl_dir.DIR_OS.$filename;
		return $_tpl_file;
	}

	/**
   	 * 获取当前编译文件路径
   	 * @return  string 		返回编译文件路径
	 */
	private function get_compile_file( $filename = NULL ) {
		if ( $filename == NULL ) $filename = $this->fileName;
		//获取当前模板文件目录
		$_comp_dir = $this->compileDir.DIR_OS.$this->module;
		if ( strpos("../", $filename) === FALSE ) {
			$_path = explode("/", $filename);
			foreach ( $_path as $_file ) {
				if ( $_file == ".." ) {
					$_comp_dir = dirname($_comp_dir);
				}
			}
			$filename = str_replace("../", "", $filename);
		}
		$filename = str_replace("/", DIR_OS, $filename);
		$_comp_file = $_comp_dir.DIR_OS.$filename.".php";
		return $_comp_file;
	}
	
	/**
	 * get args from arguments string
	 * @param		array		$_var		变量数组
	 * @param		string		$_str		参数字符串
	 * @param	 	string		$_limit		参数分隔符
	 */
	private function getArgs(&$_var, $_str, $_limit = NULL ) {
		
		if ( $_limit == NULL ) $_limit = '=';
		$_idx = strpos($_str, $_limit);
		$_name = '';
		$_val = '';
		if ( $_idx !== FALSE ) {
			$_name = trim(substr($_str, 0, $_idx));
			$_val = trim(substr($_str, $_idx + strlen($_limit)));
		}
		if ( $_name != '' ) $_var[$_name] = $_val;
		
	}
	
	/**
	 * 获取include路径
     * 参数格式说明：'home:public.top'
     * home 应用名称，应用名称与路径信息用‘:’号分隔, 如果没有申明应用则默认以当前的应用为相对路径
	 * @param		string		$_file_path	        被包含的文件路径
	 */
	public function getIncludePath( $_file_path = NULL ) {
		
	    if ( !$_file_path ) return;
        $_home = HttpRequest::$_request['app_name'];         //默认使用当前应用的目录
        if ( ($_pos_1 = strpos($_file_path, ':')) !== FALSE ) $_home = substr($_file_path, 0, $_pos_1);
        if ( ($_pos_2 = strrpos($_file_path, '.')) !== FALSE ) $_file = substr($_file_path, $_pos_2+1);
        else return '';
        if ( $_pos_1 !== FALSE ) $_path_org = substr($_file_path, $_pos_1+1, ($_pos_2-$_pos_1));
        $_path = ROOT.DIR_OS.$_home.DIR_OS.str_replace('.', DIR_OS, $_path_org);
         
		$_tpl_file = $_path. SysCfg::$temp_dir.DIR_OS.Herosphp::getAppConfig('temp_dir').DIR_OS.$_file.'.html';
		$_comp_file = $this->compileDir.DIR_OS.str_replace('.', DIR_OS, $_path_org).$_file.'.html.php';
		
		//编译包含的文件
		if ( !file_exists($_comp_file) 
		|| (!$this->cache && filemtime($_tpl_file) > filemtime($_comp_file))
		|| $this->cache == -1 ) {
			$this->complieTemplate($_tpl_file, $_comp_file);	
		}
		return $_comp_file;
	
	}
	
	/**
	 * 引进静态资源如css，js
	 * 
	 * @param		string		$_type 资源类别
	 * @param		string		$_file_path 资源路径
	 */
	public function importResource( $_type, $_file_path ) {
		
		if ( $_type == '' || $_file_path == '' ) return;
		
		$_temp = self::$_res_template[$_type];
		$_url = SysCfg::$static_url.'/'.SysCfg::$static_dir.'/';
		$_file_info = explode(":", $_file_path);
		$_url .= $_file_info[0].'/';
		//js文件放在应用的res根目录,css放入皮肤文件夹
		if ( $_file_info[0] != 'public' && $_type == 'css' ) 
			$_url .= 'skin/'.Herosphp::getAppConfig('skin').'/';
		
		if ( $_type == 'packcss' || $_type == 'packjs' ) {
			$_url .= $_file_info[1];
			$__type = str_replace("pack", '', $_type);
			$_temp = self::$_res_template[$__type];
		} else {
			$_url .= $_type.'/'.$_file_info[1];
		}
		
		return str_replace('{url}', $_url, $_temp);

	}

	/**
	 * get the executed static html content. <br />
	 * 获取模板执行后的代码
	 * @param	$_tpl_file
	 * @return	$_html
	*/
	public function &getExecutedHtml( $_tpl_file = NULL ) {
		
		ob_start();
		$this->display( $_tpl_file );
		$_html = ob_get_contents();
		ob_end_clean();
		return  $_html;
	
	}

}
?>