<?php
namespace common\action;

use herosphp\bean\Beans;
use herosphp\core\Controller;
use herosphp\core\WebApplication;
use herosphp\http\HttpRequest;
use herosphp\utils\Page;

define('COM_ERR_MSG', '(⊙o⊙) 系统出了小差！');

/**
 * 模块通用 Action
 * @author          yangjian<yangjian102621@gmail.com>
 */
abstract class CommonAction extends Controller {}