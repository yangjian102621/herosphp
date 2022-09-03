<?php
declare(strict_types=1);
namespace app\entity;

use herosLdb\Model;

/**
 * @property string $id
 * @property string $email
 */
class User extends Model
{
    protected $connection = 'default';

    protected $table = 'user';

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'email'
    ];
}
