<?php

namespace tasks;

use gmodel\GModel;
use tasks\interfaces\ITask;
use herosphp\core\Loader;

Loader::import('tasks.interfaces.ITask', IMPORT_CLIENT);
Loader::import("extends.gmodel.GModel", IMPORT_CUSTOM);
/**
 * 根据database.xml文档创建数据库。同时生成Model, Dao, Service层
 * @author yangjian<yangjian102621@gmail.com>
 *
 */
class GmodelTask implements ITask {

    public function run() {

        $gmodel = new GModel($_SERVER["argv"][2], $_SERVER["argv"][3]);
        $gmodel->execute();
        //$gmodel
    }

}
