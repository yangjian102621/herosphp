<?php
declare(strict_types=1);

namespace app\controller;

use herosphp\validate\Validate;

class UserValidate extends Validate
{
    protected array $rule = [
        'name' => 'require|max:25',
        'age' => 'require|number|between:1,120',
        'email' => 'email',
    ];

    protected array $message = [
        'name.require' => '名称必须',
        'name.max' => '名称最多不能超过25个字符',
        'age.number' => '年龄必须是数字',
        'age.between' => '年龄只能在1-120之间',
        'email' => '邮箱格式错误',
    ];

    protected array $scene = [
        'add' => ['name','age'],
        'edit' => ['name','age','email'],
    ];
}
