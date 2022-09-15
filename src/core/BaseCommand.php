<?php

// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// * Copyright 2014 The Herosphp Authors. All rights reserved.
// * Use of this source code is governed by a MIT-style license
// * that can be found in the LICENSE file.
// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

declare(strict_types=1);

/**
 * 命令行控制器基类
 * ---------------------------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 */

namespace herosphp\core;

use herosphp\utils\Logger;

abstract class BaseCommand
{
    public const CLI_PROC_RUNNING = 0;

    public const CLI_PROC_EXIT = 1;

    protected static $_signalMapping = [
        SIGHUP,    // Hangup (POSIX)
        SIGINT,    // Interrupt (ANSI)
        SIGQUIT,    // Quit (POSIX)
        SIGTERM,    // Termination (ANSI)
    ];

    // process run state
    protected int $_processState = 0;

    public function __init()
    {
        $this->_processState = self::CLI_PROC_RUNNING;

        // register signal
        foreach (static::$_signalMapping as $val) {
            $this->registerSignal($val);
        }
    }

    public function registerSignal(int $signo): bool
    {
        Logger::info($signo);
        return pcntl_signal($signo, [$this, 'signalHandler']);
    }

    public function signalHandler(int $signo)
    {
        switch ($signo) {
            case SIGHUP:
            case SIGINT:
            case SIGQUIT:
            case SIGTERM:
                Logger::warn('Reciving an interupt signal, program is exiting...');
                $this->_processState = self::CLI_PROC_EXIT;
                break;
            default:
                break;
        }
    }

    public function isRunning(): bool
    {
        pcntl_signal_dispatch();
        if ($this->_processState === self::CLI_PROC_EXIT) {
            return false;
        }
        return true;
    }
}
