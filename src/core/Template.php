<?php

// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// * Copyright 2014 The Herosphp Authors. All rights reserved.
// * Use of this source code is governed by a MIT-style license
// * that can be found in the LICENSE file.
// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

declare(strict_types=1);

namespace herosphp\core;

use herosphp\files\FileUtils;

/**
 * 模板编译类,将数据模型导入到模板并输出。
 * ---------------------------------------------------------------------
 * @author RockYang<yangjian102621@gmail.com>
 */
class Template
{
    // 通过 assign 函数注册的变量临时存放数组
    private array $_temp_var = [];

    // 模板目录
    private string $_temp_dir = "";

    // 编译目录
    private string $_compile_dir = "";

    // 是否开启编译缓存
    private static bool $_cache = true;

    // 模板编译规则
    private static array $_temp_rules = array(
        /**
         * 输出变量,数组
         * {$varname}, {$array['key']}
         */
        '/{\$([^\}|\.]{1,})}/i' => '<?php echo \$${1}?>',
        /**
         * 以 {$array.key} 形式输出一维数组元素
         */
        '/{\$([0-9a-z_]{1,})\.([0-9a-z_]{1,})}/i'    => '<?php echo \$${1}[\'${2}\']?>',
        /**
         * 以 {$array.key1.key2} 形式输出二维数组
         */
        '/{\$([0-9a-z_]{1,})\.([0-9a-z_]{1,})\.([0-9a-z_]{1,})}/i'    => '<?php echo \$${1}[\'${2}\'][\'${3}\']?>',

        //for 循环
        '/{for ([^\}]+)}/i'    => '<?php for ${1} {?>',
        '/{\/for}/i'    => '<?php } ?>',

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
        '/{loop\s+\$(.*?)\s+\$([0-9a-z_]{1,})\s*}/i'    => '<?php foreach ( \$${1} as \$${2} ) { ?>',
        '/{\/loop}/i'    => '<?php } ?>',

        /**
         * {expr}标签： 执行php表达式
         * {echo}标签：输出php表达式
         * {url}标签：输出格式化的url
         * {date}标签：根据时间戳输出格式化日期
         * {cut}标签：裁剪字指定长度的字符串,注意截取的格式是UTF-8,多余的字符会用...表示
         */
        '/{expr\s+(.*?)}/i'   => '<?php ${1} ?>',
        '/{echo\s+(.*?)}/i'   => '<?php echo ${1} ?>',
        '/{date\s+(.*?)(\s+(.*?))?}/i'   => '<?php echo $this->getDate(${1}, "${2}") ?>',
        '/{cut\s+(.*?)(\s+(.*?))?}/i'   => '<?php echo $this->cutString(${1}, "${2}") ?>',

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

        '/{(res)\s+([^\}]+)\s*}/i'
        => '<?php echo $this->getResourceURL(\'${2}\')?>',

        /**
         * 引入静态资源 css file,javascript file
         */
        '/{(res):([a-z]{1,})\s+([^\}]+)\s*}/i'
        => '<?php echo $this->importResource(\'${2}\', "${3}")?>'
    );


    // 静态资源模板
    private static array $_res_temp = [
        'css'    => "<link rel=\"stylesheet\" type=\"text/css\" href=\"{url}\" />\n",
        'less'    => "<link rel=\"stylesheet/less\" type=\"text/css\" href=\"{url}\" />\n",
        'js'    => "<script charset=\"utf-8\" type=\"text/javascript\" src=\"{url}\"></script>\n"
    ];


    public function __construct()
    {
        $configs = Config::getValue('app', 'template');
        $this->addRules($configs['rules']);
        $skin = $configs['skin'];

        $debug = Config::getValue('app', 'debug');
        if ($debug == true) {
            static::$_cache = false;
        } else {
            static::$_cache = true;
        }

        $this->_temp_dir = APP_PATH . "views/{$skin}/";
        $this->_compile_dir = RUNTIME_PATH . "views/{$skin}/";
    }

    /**
     * 增加模板替换规则
     * @param array $rules
     */
    public  function addRules(array $rules): void
    {
        if (is_array($rules) && !empty($rules)) {
            static::$_temp_rules = array_merge(static::$_temp_rules, $rules);
        }
    }

    /**
     * compile template
     */
    private function complieTemplate(string $tempFile, string $compileFile): void
    {
        // use compile cache
        if (file_exists($compileFile) && static::$_cache === true) {
            return;
        }

        // compile template 
        $content = @file_get_contents($tempFile);
        if ($content === false) {
            E("加载模板文件 {" . $tempFile . "} 失败！请在相应的目录建立模板文件。");
        }
        $content = preg_replace(array_keys(static::$_temp_rules), static::$_temp_rules, $content);

        // create compile dir
        if (!file_exists(dirname($compileFile))) {
            FileUtils::makeFileDirs(dirname($compileFile));
        }

        // create compile file
        if (!file_put_contents($compileFile, $content, LOCK_EX)) {
            E("生成编译文件 {$compileFile} 失败。");
        }
    }

    /**
     * extract template vars and render template content
     */
    public function display(string $tempFile): void
    {
        if (empty($tempFile)) {
            return;
        }

        $compileFile = $tempFile . '.php';
        if (file_exists($this->_temp_dir . $tempFile)) {
            $this->complieTemplate($this->_temp_dir . $tempFile, $this->_compile_dir . $compileFile);
            extract($this->_temp_var);    // extract the temp vars
            include $this->_compile_dir . $compileFile;
        } else {
            E("要编译的模板[" . $this->_temp_dir . $tempFile . "] 不存在！");
        }
    }

    /**
     * 获取被包含模板的路径，路径使用 . 替代 /, 如: user.profile => user/profile.html
     */
    public function getIncludePath($tempPath = null): string
    {
        if (empty($tempPath)) {
            return '';
        }

        $filename = str_replace('.', '/', $tempPath);
        $tempFile = $this->_temp_dir . $filename;
        $compileFile = $this->_compile_dir . $filename . '.php';

        $this->complieTemplate($tempFile, $compileFile);
        return $compileFile;
    }

    /**
     * 获取日期
     * @param $time
     * @param $format
     * @return string
     */
    private function _getDate($time, $format)
    {

        if (!$time) return '';
        if (!$format) $format = 'Y-m-d H:i:s';
        return date($format, $time);
    }

    /**
     * 裁剪字符串，使用utf-8编码裁剪
     * @param $str 要裁剪的字符串
     * @param $length 字符串长度
     * @return string
     */
    private function _cutString($str, $length)
    {

        if (mb_strlen($str, 'UTF-8') <= $length) {
            return $str;
        }
        return mb_substr($str, 0, $length, 'UTF-8') . '...';
    }

    /**
     * 引进静态资源如css，js
     * @param string $type 资源类别
     * @param string $path 资源路径
     * @return string
     */
    public function importResource($type, $path)
    {
        $template = static::$_res_temp[$type];
        $result = str_replace('{url}', $path, $template);
        return $result;
    }

    /**
     * 获取页面执行后的代码
     * @param	string $tempFile
     * @return	string $html
     */
    public function &getExecutedHtml($tempFile)
    {
        ob_start();
        $this->display($tempFile);
        $html = ob_get_contents();
        ob_end_clean();
        return  $html;
    }
}
