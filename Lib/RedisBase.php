<?php

class RedisBase {
	/**
	 * redis @var Redis
	 */
	private $redis;

	function __construct() {
		$this->redis = new Redis();
		$host = config("REDIS_HOST");
		$this->redis->connect($host, config("REDIS_PORT"));
		$password = config("REDIS_PASSWORD");
		if (!is_null($password)) {
            $this->redis->auth($password);
        }
	}

	public function expire($key, $ttl) {
		return $this->redis->expire($key, $ttl);
	}

	public function ttl($key) {
		return $this->redis->ttl($key);
	}

	public function del($key) {
		return $this->redis->del($key);
	}

	public function rEval($lua, $args, $numKeys = 0) {
		return $this->redis->eval($lua, $args, $numKeys);
	}

	public function renameNx($key, $newKey) {
		return $this->redis->renameNx($key, $newKey);
	}

	public function RedisHash($key) {
		return new RedisHash($key, false, $this->redis);
	}

	public function RedisList($key) {
		return new RedisList($key, false, $this->redis);
	}

	public function RedisSet($key) {
		return new RedisSet($key, false, $this->redis);
	}

	public function RedisString($key) {
		return new RedisString($key, false, $this->redis);
	}

	public function watch($key) {
		return $this->redis->watch($key);
	}

	public function unwatch() {
		return $this->redis->unwatch();
	}

	public function begin() {
		return $this->redis->multi();
	}

	public function commit() {
		return $this->redis->exec();
	}

	public function rollback() {
		return $this->redis->discard();
	}

	public function info() {
		return $this->redis->info();
	}

	public function DBSize() {
		return $this->redis->dbSize();
	}

	public function randomKey() {
		return $this->redis->randomKey();
	}

	public function type($key) {
		$map = [
			0 => "None",
			1 => "String",
			2 => "List",
			3 => "Set",
			4 => "ZSet",
			5 => "Hash",
		];
		return $map[$this->redis->type($key)];
	}

	public function selectDB($index) {
		return $this->redis->select($index);
	}

	// Warn 需要用的时候再开
	private function flushDB() {
		return $this->redis->flushDB();
	}
}