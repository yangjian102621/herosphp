<?php

namespace test\dao;

use test\dao\interfaces\IArticleDao;
use common\dao\CommonDao;
use herosphp\core\Loader;

Loader::import('test.dao.interfaces.IArticleDao');
/**
 * 文章(DAO)接口实现
 * Class ArticleDao
 * @package test\dao
 * @author yangjian102621@gmail.com
 */
class ArticleDao extends CommonDao implements IArticleDao {}