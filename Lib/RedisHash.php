<?php

class RedisHash {
	/**
	 * redis @var Redis
	 */
	private $redis;
	private $key;

	function __construct($key, $connect = true, $redis = null) {
		$this->key = $key;
		if ($connect) {
			$this->redis = new Redis();
            $host = config("REDIS_HOST");
            $this->redis->connect($host, config("REDIS_PORT"));
            $password = config("REDIS_PASSWORD");
			if (!is_null($password))
				$this->redis->auth($password);
		} else {
			$this->redis = $redis;
		}
	}

	public function set($key, $val) {
		return $this->redis->hSet($this->key, $key, $val);
	}

	public function add($key, $val) {
		return $this->redis->hSetNx($this->key, $key, $val);
	}

	public function mSet($kvs) {
		return $this->redis->hMset($this->key, $kvs);
	}

	public function get($key) {
		return $this->redis->hGet($this->key, $key);
	}

	public function mGet($keys) {
		return $this->redis->hMGet($this->key, $keys);
	}

	public function getAll() {
		return $this->redis->hGetAll($this->key);
	}

	public function remove($key) {
		return $this->redis->hDel($this->key, $key);
	}

	public function size() {
		return $this->redis->hLen($this->key);
	}

	public function isMember($key) {
		return $this->redis->hExists($this->key, $key);
	}

	public function incrBy($key, $inc) {
		return $this->redis->hIncrBy($this->key, $key, $inc);
	}

	public function keys() {
		return $this->redis->hKeys($this->key);
	}

	public function vals() {
		return $this->redis->hVals($this->key);
	}
}