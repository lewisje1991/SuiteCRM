<?php

/**
 * Created by PhpStorm.
 * User: lewis
 * Date: 01/11/16
 * Time: 16:16
 */
class LewisTestClass
{
    /**
     * @var DBManager
     */
    private $db;

    /**
     * @var array
     */
    private $config;

    public function __construct(DBManager $db, $config) {
        $this->db = $db;
        $this->config = $config;
    }
}