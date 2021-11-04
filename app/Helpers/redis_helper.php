<?php

/*
@return $redis object
examples
$redis = RedisConnect();
*/
function RedisConnect() {
    $redis = new Redis() or false;
    $redis->connect(env('redis.host'), env('redis.port'));
    $redis->auth(env('redis.password'));
    $redis->select(env('redis.database'));
    return $redis;
}
