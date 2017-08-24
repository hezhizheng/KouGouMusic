<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/8/24
 * Time: 1:01
 */

// 获取参数，第一为控制器，第二个为方法，第0个为调用的文件路径
// var_dump($argv);
// exit;
$c = $argv[1];
$a = $argv[2];
//拼出类文件路径, 如果a为index crontab_path = index.controller.php
//$crontab_path = 'controller/' . $c . '.controller.php';
$crontab_path = './' . $c . '.php';

//引入该文件
require $crontab_path;
//实例化类
$controller = new $c;
//调用该方法
$controller->$a();