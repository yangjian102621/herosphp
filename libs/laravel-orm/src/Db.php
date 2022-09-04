<?php
declare(strict_types=1);

namespace herosLdb;

use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\Connection;

/**
 * Class Db
 *
 * @method static array select(string $query, $bindings = [], $useReadPdo = true)
 * @method static int insert(string $query, $bindings = [])
 * @method static int update(string $query, $bindings = [])
 * @method static int delete(string $query, $bindings = [])
 * @method static bool statement(string $query, $bindings = [])
 * @method static mixed transaction(\Closure $callback, $attempts = 1)
 * @method static void beginTransaction()
 * @method static void rollBack($toLevel = null)
 * @method static void commit()
 * @method static Connection connection($connection = null)
 * @method static mixed listen(\Closure $callback)
 */

class Db extends Manager
{
}
