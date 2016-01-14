<?php
namespace test\service;

use test\service\interfaces\IArticleService;
use common\service\CommonService;
use herosphp\core\Loader;

Loader::import('test.service.interfaces.IArticleService', IMPORT_APP);
Loader::import('common.service.CommonService', IMPORT_APP);

/**
 * 文章服务接口实现
 * Class ArticleService
 * @package test\service
 */
class ArticleService extends CommonService implements IArticleService {}
