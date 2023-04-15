<?php
/**
 * Created by PhpStorm.
 * User: jesse
 * Date: 2023/4/15
 * Time: 14:05
 */

require __DIR__ . '/vendor/autoload.php';



use Floridaman963\PhpRedisLock\RedisLock\RedisLock;



set_time_limit(20);
$redis = new \Redis();
$redis->connect('127.0.0.1', 6379, 2.5); // 2.5 sec timeout.




$uuid = uniqid('uuid',true);
$lockName = "lock";

RedisLock::lock($redis,$lockName,$uuid);


var_dump('执行...');
sleep(2);
var_dump('执行over');

RedisLock::unlock($redis,$lockName,$uuid);

