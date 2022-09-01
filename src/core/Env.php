<?php
declare(strict_types=1);

namespace herosphp\core;

/**
 * 环境配置
 * @author chenzifan
 */
class Env
{
    protected array $env = [];

    /**
     * 加载配置文件.
     */
    public function __construct()
    {
        $envFile = BASE_PATH . '.env';
        if (file_exists($envFile)) {
            $str = @file_get_contents($envFile);
            $arr = explode("\n", $str);
            foreach ($arr ?? [] as $v) {
                $v = $this->parse($v);
                if ($v) {
                    $this->env[$v[0]] = $v[1];
                }
            }
        }
    }

    /**
     * 获取环境变量.
     *
     * @param  string  $key
     * @param  null  $default
     * @return mixed
     */
    public function get(string $key, $default = null): mixed
    {
        return $this->env[$key] ?? $default;
    }

    /**
     * 解析加载项.
     *
     * @param  string  $str
     * @return null|array
     */
    protected function parse(string $str): ?array
    {
        $r = strpos($str, '=');
        if (! $r) {
            return null;
        }
        $key = trim(substr($str, 0, $r));
        if (! $key) {
            return null;
        }
        $j = strpos($str, '#');
        if ($j === false) {
            $val = trim(substr($str, $r + 1));
        } else {
            $val = trim(substr($str, $r + 1, $j - $r - 1));
        }
        return match ($val) {
            'true', '(true)' => [$key, true],
            'false', '(false)' => [$key, false],
            'empty', '(empty)' => [$key, ''],
            'null', '(null)' => [$key, null],
            default => [$key, $val],
        };
    }
}
