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
	private $userDao = null;

	/**
	 * @var \herosphp\model\C_Model
	 */
	private $newsDao = null;

	/**
	 * @var \herosphp\model\C_Model
	 */
	private $adminDao = null;

	/**
	 * @param $userModel
	 * @param $newsModel
	 * @param $adminModel
	 */
	public function __construct($userModel, $newsModel, $adminModel) {
		$this->setModelDao(Loader::model($userModel));
		$this->userDao = Loader::model($newsModel);
		$this->newsDao = Loader::model($adminModel);
	}
}
