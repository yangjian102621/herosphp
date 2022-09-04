<?php

// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// * Copyright 2014 The Herosphp Authors. All rights reserved.
// * Use of this source code is governed by a MIT-style license
// * that can be found in the LICENSE file.
// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

declare(strict_types=1);

namespace herosphp\upload;

/**
 * upload file save handler
 * ---------------------------------------------------
 * @author RockYang<yangjian102621@gmail.com>
 */
interface IFileSaveHandler
{
    // save upload file and return the saved path|url
    public function save(string $srcFile, string $filename): string|false;

    public function saveBase64($data, string $filename): string|false;
}
