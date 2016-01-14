<?php

namespace test\dao;

use test\dao\interfaces\IArticleDao;
use common\dao\CommonDao;
use herosphp\core\Loader;

Loader::import('test.dao.interfaces.IArticleDao');
Loader::import('common.dao.CommonDao');

/**
 * 文章(DAO)接口实现
 * Class ArticleDao
 * @package test\dao
 * @author yangjian102621@163.com
 */
class ArticleDao extends CommonDao implements IArticleDao {}