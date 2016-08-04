<?php

namespace tasks;

use herosphp\lock\SynLockFactory;
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

            $lock = SynLockFactory::getFileSynLock(0x1234);
            tprintError("try to get the lock....");
            $lock->tryLock();
            tprintOk("get the lock.");
            sleep(10);
            tprintWarning("release the lock.");
            $lock->unlock();

        }

} 
