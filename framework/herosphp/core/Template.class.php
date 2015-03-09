<?php
/*---------------------------------------------------------------------
 * 模板编译类。将数据模型导入到模板并输出。
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@163.com>
 *-----------------------------------------------------------------------*/

namespace herosphp\core;

use herosphp\utils\FileUtils;

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
        '/{\$([^\}|\.]{1,})}/i' => '<?php echo \$${1}?>',
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
        '/{(res|gres|cres):([a-z]{1,})\s+([^\}]+)\s*}/i'
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
        $this->configs['action'] = $request->getAction();
        $this->configs['method'] = $request->getMethod();
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
     * @param  string $varname
     * @param  string $value 变量值
     */
	public function assign( $varname, $value ) {
		$this->templateVar[$varname] = $value;
	}
	
	/**
	 * 获取模板变量
	 * @param string $varname 变量名
     * @return mixed
	 */
	public function getTemplateVar( $varname ) {
		return $this->templateVar[$varname];
	}

	/**
	 * 编译模板
	 * @param 		string 		$tempFile 	 	模板文件路径
	 * @param		string		$compileFile	编译文件路径
	 */
	private function complieTemplate( $tempFile, $compileFile ) {

        //根据缓存情况编译模板
        if ( !file_exists($compileFile)
            || ($this->cache == 1 && filemtime($compileFile) < filemtime($tempFile))
            || $this->cache == 0 ) {

            //获取模板文件
            $content = @file_get_contents($tempFile);
            if ( $content == FALSE ) {
                E("加载模板文件 {".$tempFile."} 失败！请在相应的目录建立模板文件。");
            }
            //替换模板
            $content = preg_replace(array_keys(self::$tempRules), self::$tempRules, $content);
            //生成编译目录
            if ( !file_exists(dirname($compileFile)) ) {
                FileUtils::makeFileDirs(dirname($compileFile));
            }

            //生成php文件
            if ( !file_put_contents($compileFile, $content, LOCK_EX) ) {
                E("生成编译文件 {$compileFile} 失败。");
            }
        }

	}

	/**
	 * 显示模板
	 * @param		string		$tempFile		模板文件名称
	 */
	public function display( $tempFile=null ) {
		
		//如果没有传入模板文件，则访问默认模块下的默认模板
        if ( !$tempFile ) {
            $tempFile = $this->configs['action'].'_'.$this->configs['method'].EXT_TPL;
        } else {
            $tempFile .= EXT_TPL;
        }
        $compileFile = $tempFile.'.php';
		if ( file_exists($this->templateDir.$tempFile) ) {
            $this->complieTemplate($this->templateDir.$tempFile, $this->compileDir.$compileFile);
			extract($this->templateVar);	//分配变量
			include $this->compileDir.$compileFile;		//包含编译生成的文件
		} else {
			E("要编译的模板[{$tempFile}] 不存在！");
		}
		
	}

	/**
	 * 获取include路径
     * 参数格式说明：app:module.templateName
     * 'home:public.top'
     * 如果没有申明应用则默认以当前的应用为相对路径
	 * @param string $tempPath	        被包含的模板路径
     * @return string
	 */
	public function getIncludePath( $tempPath = null ) {
		
	    if ( !$tempPath ) return '';
        if ( strpos($tempPath, ':') === FALSE ) {
            $appName = APP_NAME;    //默认为当前应用
        } else {
            $pathInfo = explode(':', $tempPath);
            $appName = $pathInfo[0];
            $tempPath = $pathInfo[1];
        }
        //切割module.templateName,找到对应模块的模板
        $moduleInfo = explode('.', $tempPath);

        $this->templateDir = APP_PATH.APP_NAME.'/'.$this->configs['module'].'/template/'.$this->configs['template'].'/';
        $this->compileDir = APP_RUNTIME_PATH.'views/'.APP_NAME.'/'.$this->configs['module'].'/';
        $tempDir = APP_PATH.APP_NAME.'/'.$moduleInfo[0].'/template/'.$this->configs['template'].'/';
        $compileDir = APP_RUNTIME_PATH.'views/'.$appName.'/'.$moduleInfo[0].'/';
        $filename = $moduleInfo[1].EXT_TPL;   //模板文件名称
        $tempFile = $tempDir.$filename;
        $compileFile = $compileDir.$filename.'.php';
        //编译文件
        $this->complieTemplate($tempFile, $compileFile);
		return $compileFile;
	}
	
	/**
	 * 引进静态资源如css，js
     * @param string $section 资源所属片区(res => 模块内部的资源, gres => 全局资源)
	 * @param string $type 资源类别
	 * @param string $path 资源路径
     * @return string
	 */
	public function importResource( $section, $type, $path ) {
        //获取资源的目录
        $resUrl = $this->configs['res_url'].RES_PATH;

        switch ( $section ) {
            case 'gres':
                $resUrl .= 'global/';
                break;

            case 'res' :
                $resUrl .= 'app/'.APP_NAME."/{$this->configs['template']}/";
                break;

            case 'cres' :
                break;
        }
        if ( $type == 'css' && $section == 'res' ) {
            $resUrl .= "skin/{$this->configs['skin']}/";
        }

        if ( $section != 'cres' ) { //包含全局或应用的静态资源
            $resUrl .= $type.'/'.$path;
        } else {    //包含自定义路径静态资源
            $resUrl .= $path;
        }
        $template = self::$resTemplate[$type];
        $result = str_replace('{url}', $resUrl, $template);

        return $result;
	}

	/**
	 * 获取页面执行后的代码
	 * @param	string $tempFile
	 * @return	string $html
	*/
	public function &getExecutedHtml( $tempFile ) {
		
		ob_start();
		$this->display( $tempFile );
        $html = ob_get_contents();
		ob_end_clean();
		return  $html;
	
	}

}
?>