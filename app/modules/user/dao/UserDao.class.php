<?php
namespace user\dao;

use user\dao\interfaces\IUserDao;
use common\dao\CommonDao;
use herosphp\core\Loader;

Loader::import('user.dao.interfaces.IUserDao');

/**
 * user(DAO)接口实现
 * @package user\dao
 * @author yangjian<yangjian102621@gmail.com>
 */
class UserDao extends CommonDao implements IUserDao {

	/**
	 * @var \herosphp\model\C_Model
	 */
	private $newsDao = null;

	/**
	 * @var \herosphp\model\C_Model
	 */
	private $adminDao = null;

	/**
	 * @var \herosphp\model\C_Model
	 */
	private $adminRoleDao = null;

	/**
	 * @param $newsModel
	 * @param $adminModel
	 * @param $adminRoleModel
	 * @param $userModel
	 */
	public function __construct($newsModel, $adminModel, $adminRoleModel, $userModel) {
		$this->newsDao = Loader::model($newsModel);
		$this->adminDao = Loader::model($adminModel);
		$this->adminRoleDao = Loader::model($adminRoleModel);
		$this->setModelDao(Loader::model($userModel));
	}
}
