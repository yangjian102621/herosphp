<?php
namespace user\service;

use user\service\interfaces\IAdminService;
use common\service\CommonService;
use herosphp\core\Loader;

Loader::import('user.service.interfaces.IAdminService');

/**
 * user(Service)接口实现
 * @package user\service
 * @author yangjian<yangjian102621@gmail.com>
 */
class AdminService extends CommonService implements IAdminService {}
