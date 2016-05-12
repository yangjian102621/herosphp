<?php
namespace user\service;

use user\service\interfaces\INewsService;
use common\service\CommonService;
use herosphp\core\Loader;

Loader::import('user.service.interfaces.INewsService');

/**
 * user(Service)接口实现
 * @package user\service
 * @author yangjian<yangjian102621@gmail.com>
 */
class NewsService extends CommonService implements INewsService {}
