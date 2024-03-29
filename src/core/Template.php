<?php

// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// * Copyright 2014 The Herosphp Authors. All rights reserved.
// * Use of this source code is governed by a MIT-style license
// * that can be found in the LICENSE file.
// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

declare(strict_types=1);

namespace herosphp\core;

use herosphp\exception\TempateException;
use herosphp\GF;
use herosphp\utils\FileUtil;

/**
 * 模板编译类,将数据模型导入到模板并输出。
 * ---------------------------------------------------------------------
 * @author RockYang<yangjian102621@gmail.com>
 */
class Template
{
    // template root dir
    private string $_temp_dir = '';

    // compile root dir
    private string $_compile_dir = '';

    // switch for template cache
    private static bool $_cache = true;

    private static string $_temp_suffix = '.html';

    // template compile rules
    private static array $_temp_rules = [

        // {$var}, {$array['key']}
        '/{\$([^\}|\.]{1,})}/i' => '<?php echo \$${1}?>',

        // array: {$array.key}
        '/{\$([0-9a-z_]{1,})\.([0-9a-z_]{1,})}/i' => '<?php echo \$${1}[\'${2}\']?>',

        // two-demensional array
        '/{\$([0-9a-z_]{1,})\.([0-9a-z_]{1,})\.([0-9a-z_]{1,})}/i' => '<?php echo \$${1}[\'${2}\'][\'${3}\']?>',

        // for loop
        '/{for ([^\}]+)}/i' => '<?php for ${1} {?>',
        '/{\/for}/i' => '<?php } ?>',

        // foreach ( $array as $key => $value )
        '/{loop\s+\$([^\}]{1,})\s+\$([^\}]{1,})\s+\$([^\}]{1,})\s*}/i' => '<?php foreach ( \$${1} as \$${2} => \$${3} ) { ?>',
        '/{\/loop}/i' => '<?php } ?>',

        // foreach ( $array as $value )
        '/{loop\s+\$(.*?)\s+\$([0-9a-z_]{1,})\s*}/i' => '<?php foreach ( \$${1} as \$${2} ) { ?>',

        // expr: excute the php expression
        // echo: print the php expression
        '/{expr\s+(.*?)}/i' => '<?php ${1} ?>',
        '/{echo\s+(.*?)}/i' => '<?php echo ${1} ?>',

        // if else tag
        '/{if\s+(.*?)}/i' => '<?php if ( ${1} ) { ?>',
        '/{else}/i' => '<?php } else { ?>',
        '/{elseif\s+(.*?)}/i' => '<?php } elseif ( ${1} ) { ?>',
        '/{\/if}/i' => '<?php } ?>',

        // require|include tag
        '/{(require|include)\s{1,}([0-9a-z_\.]{1,})\s*}/i'
        => '<?php include $this->_getIncludePath(\'${2}\')?>',

        // tag to import css file,javascript file
        '/{(res):([a-z]{1,})\s+([^\}]+)\s*}/i'
        => '<?php echo $this->_importResource(\'${2}\', "${3}")?>'
    ];

    // static resource
    private static array $_res_temp = [
        'css' => "<link rel=\"stylesheet\" type=\"text/css\" href=\"{url}\" />\n",
        'less' => "<link rel=\"stylesheet/less\" type=\"text/css\" href=\"{url}\" />\n",
        'js' => "<script charset=\"utf-8\" type=\"text/javascript\" src=\"{url}\"></script>\n"
    ];

    public function __construct()
    {
        $configs = GF::getAppConfig('template');
        $skin = $configs['skin'];

        if (!empty($configs['rules'])) {
            $this->addRules($configs['rules']);
        }

        $debug = GF::getAppConfig('debug');
        if ($debug == true) {
            self::$_cache = false;
        } else {
            self::$_cache = true;
        }

        $this->_temp_dir = APP_PATH . "views/{$skin}/";
        $this->_compile_dir = RUNTIME_PATH . "views/{$skin}/";
    }

    // add new template rules
    protected function addRules(array $rules): void
    {
        if (is_array($rules) && !empty($rules)) {
            self::$_temp_rules = array_merge(self::$_temp_rules, $rules);
        }
    }

    // 获取页面执行后的代码
    protected function getExecutedHtml($file, $data): string
    {
        if (empty($file)) {
            throw new TempateException('Template file is needed.');
        }

        $tempFile = $this->_temp_dir . $file . self::$_temp_suffix;
        $compileFile = $this->_compile_dir . $file . '.php';
        if (!file_exists($tempFile)) {
            throw new TempateException("template file {$tempFile} not found.");
        }

        ob_start();
        $this->_compileTemplate($tempFile, $compileFile);
        extract($data);
        include $compileFile;

        $html = ob_get_contents();
        ob_end_clean();
        return  $html;
    }

    // 获取被包含模板的路径，路径使用 . 替代 /, 如: user.profile => user/profile.html
    private function _getIncludePath($tempPath = null): string
    {
        if (empty($tempPath)) {
            return '';
        }

        $filename = str_replace('.', '/', $tempPath);
        $tempFile = $this->_temp_dir . $filename . self::$_temp_suffix;
        $compileFile = $this->_compile_dir . $filename . '.php';
        $this->_compileTemplate($tempFile, $compileFile);
        return $compileFile;
    }

    private function _importResource($type, $path)
    {
        if (!str_starts_with($path, '/')) {
            $path = '/' . $path;
        }

        $template = self::$_res_temp[$type];
        return str_replace('{url}', $path, $template);
    }

    /**
     * compile template
     */
    private function _compileTemplate(string $tempFile, string $compileFile): void
    {
        // use compile cache
        if (file_exists($compileFile) && self::$_cache === true) {
            return;
        }

        // compile template
        $content = @file_get_contents($tempFile);
        if ($content === false) {
            throw new TempateException("failed to load template file {$tempFile}");
        }
        $content = preg_replace(array_keys(self::$_temp_rules), self::$_temp_rules, $content);

        // create compile dir
        if (!file_exists(dirname($compileFile))) {
            FileUtil::makeFileDirs(dirname($compileFile));
        }

        // create compile file
        if (!file_put_contents($compileFile, $content, LOCK_EX)) {
            throw new TempateException("failed to create compiled file {$compileFile}");
        }
    }
}
