<?php

namespace test\dao;

use test\dao\interfaces\IArticleDao;
use common\dao\CommonDao;
use herosphp\core\Loader;
use test\dao\interfaces\IUserDao;

Loader::import('test.dao.interfaces.IUserDao');
/**
 * @author yangjian102621@gmail.com
 */
class UserDao extends CommonDao implements IUserDao {}
