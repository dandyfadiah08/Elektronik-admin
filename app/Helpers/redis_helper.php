<?php

/*
@return $redis object
examples
$redis = RedisConnect();
*/
function RedisConnect() {
    $redis = false;
    try {
        $redis = new Redis();
        $redis->connect(env('redis.host'), env('redis.port'));
        $redis->auth(env('redis.password'));
        $redis->select(env('redis.database'));
    } catch (\Exception $e) {
        log_message('critical', $e->getFile()."|".$e->getLine()." : ".$e->getMessage());
    }
    return $redis;
}
