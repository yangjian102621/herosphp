<?php

namespace test\service\interfaces;
use common\service\interfaces\ICommonService;
use herosphp\core\Loader;

Loader::import('common.service.interfaces.ICommonService', IMPORT_APP);
/**
 * 文章服务接口
 * Interface IArticleService
 */
interface IArticleService extends ICommonService {}
