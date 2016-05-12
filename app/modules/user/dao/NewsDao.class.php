<?php
namespace user\dao;

use user\dao\interfaces\INewsDao;
use common\dao\CommonDao;
use herosphp\core\Loader;

Loader::import('user.dao.interfaces.INewsDao');

/**
 * user(DAO)接口实现
 * @package user\dao
 * @author yangjian<yangjian102621@gmail.com>
 */
class NewsDao extends CommonDao implements INewsDao {
}
