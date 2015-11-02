<?php

namespace test\dao\interfaces;
use common\dao\interfaces\ICommonDao;
use herosphp\core\Loader;

Loader::import("common.dao.interfaces.ICommonDao", IMPORT_APP);
/**
 * 文章(DAO)接口
 * Interface IArticleDao
 * @package test\dao\interfaces
 * @author yangjian102621@163.com
 */
interface IArticleDao extends ICommonDao {}