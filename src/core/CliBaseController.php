<?php

// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// * Copyright 2014 The Herosphp Authors. All rights reserved.
// * Use of this source code is governed by a MIT-style license
// * that can be found in the LICENSE file.
// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

declare(strict_types=1);

/**
 * 命令行控制器基类，通过命令行调用 Controller 对应 Action 的操作
 * ---------------------------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 */

namespace herosphp\core;

use herosphp\GF;

abstract class CliBaseController
{
    public const CLI_PROC_RUNNING = 0;
    public const CLI_PROC_EXIT = 1;

    protected static $_signalMapping = array(
        SIGHUP  => NULL,    // Hangup (POSIX)
        SIGINT  => NULL,    // Interrupt (ANSI)
        SIGQUIT => NULL,    // Quit (POSIX)
        SIGTERM => NULL,    // Termination (ANSI)
    );

    // process run state
    protected int $_processState = 0;

    protected function __init()
    {
        $this->_processState = static::CLI_PROC_RUNNING;

        // register signal
        foreach (static::$_signalMapping as $val) {
            $this->registerSignal($val);
        }
    }

    public function registerSignal($signo): bool
    {
        return pcntl_signal($signo, [$this, 'signalHandler']);
    }

    public function signalHandler($signo)
    {
        switch ($signo) {
            case SIGHUP:
            case SIGINT:
            case SIGQUIT:
            case SIGTERM:
                $this->_processState = static::CLI_PROC_EXIT;
                break;
            default:
                break;
        }
    }
}
