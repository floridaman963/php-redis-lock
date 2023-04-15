<?php

namespace Floridaman963\PhpRedisLock\RedisLock;




class RedisLock
{
    //

    static public function Lock(\Redis $redis,$lockName,$uuid,$ttl=30){

        //尝试获取锁，直到加锁成功
        while(!$redis->set($lockName,$uuid,['nx','ex'=>$ttl])) {
            sleep(0.5);
        };
    }


    static public function unlock(\Redis $redis,$lockName,$uuid){
        //删除锁
       /* $redis->del($lockName);*/

        //删除锁
        /*if($redis->get($lockName) === $uuid){
            $redis->del($lockName);
        }*/



        //删除锁
        $script ="
            if redis.call('get',KEYS[1]) == ARGV[1] 
            then 
                return redis.call('del',KEYS[1]) 
            else 
                return 0 
            end
        ";

        $redis->eval($script,[$lockName,$uuid],1);

    }




    //加入过期时间，就会发生锁过期，无锁裸奔
    //必须自动续期 或者设置大的过期时间


    //续期脚本 需要单独运行
    //自动续期脚本
    //这里使用消息队列方法续期
    //脚本部署在和主程序在同一台机器
    //保证主机宕机，续期脚本停止运行
    //$redis->zAdd($ExName, time(),$lockName);
    public function addExipire($lockName,$time=10){



        $redis = new Redis();
        $redis->connect('127.0.0.1', 6379, 2.5); // 2.5 sec timeout.
        $data = $redis->zRangeByScore($lockName,'-nf',time());

        foreach ($data as $item){
            $lock = $redis->get($lockName);
            if(!$lock){
                $redis->zRem($lockName,$item);
            }else{
                $redis->expire($lockName,$time);
            }
        }


    }




}
