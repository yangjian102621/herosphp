<?php
$a1 = ['aaa' => 1, 'bbb' => ['ccc' => 1]];
$a2 = ['xxx' => 11, 'bbb' => ['zzz' => 2]];

print_r(array_merge_recursive($a1, $a2));
