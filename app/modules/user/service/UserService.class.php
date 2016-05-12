<?php
namespace user\service;

use user\service\interfaces\IUserService;
use common\service\CommonService;
use herosphp\core\Loader;

Loader::import('user.service.interfaces.IUserService');

/**
 * user(Service)接口实现
 * @package user\service
 * @author yangjian<yangjian102621@gmail.com>
 */
class UserService extends CommonService implements IUserService {}
