<?php

namespace tasks;

use tasks\interfaces\ITask;
use herosphp\core\Loader;

Loader::import('tasks.interfaces.ITask', IMPORT_CLIENT);
/**
 * @author yangjian102621@gmail.com
 * @version 1.0.0
 * @since 15-4-27
 */
class TestTask implements ITask {


        public function run() {

            tprintOk("Hello, world!");

            //$model = Loader::model('article');
            //$conditions = array("id" => ">300");
           // $items = $model->getItems($conditions, "id, url, title", null, 1, 20);
           // tprintOk($items[0]['title']);
        }

} 
