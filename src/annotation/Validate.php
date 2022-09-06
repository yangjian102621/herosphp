<?php
declare(strict_types=1);

namespace herosphp\annotation;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class Validate
{
    public function __construct(public string $class, public string $scene)
    {
    }
}
