<?php
declare(strict_types=1);

namespace herosRQueue;

interface ConsumerInterface
{
    public function consume(array $data): void;
}
