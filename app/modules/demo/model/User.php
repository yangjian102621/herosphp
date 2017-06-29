<?php
/**
 * User Model
 * ------------
 * @author yangjian<yangjian102621@gmail.com>
 * @date 2017-03-20
 */

namespace app\demo\model;


class User
{
    protected $username;

    protected $password;

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

}