<?php
namespace user\dao;

use user\dao\interfaces\IAdminDao;
use common\dao\CommonDao;
use herosphp\core\Loader;

Loader::import('user.dao.interfaces.IAdminDao');

/**
 * user(DAO)接口实现
 * @package user\dao
 * @author yangjian<yangjian102621@gmail.com>
 */
class AdminDao extends CommonDao implements IAdminDao {
}
