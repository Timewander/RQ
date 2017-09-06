<?php

class RedisString {
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

	public function set($val) {
		return $this->redis->set($this->key, $val);
	}

	public function setNx($val) {
		return $this->redis->setnx($this->key, $val);
	}

	public function setEx($val, $ttl = 86400) {
		return $this->redis->setex($this->key, min($ttl, 2592000), $val);
	}

	public function get() {
		return $this->redis->get($this->key);
	}

	public function append($add) {
		return $this->redis->append($this->key, $add);
	}

	public function length() {
		return $this->redis->strlen($this->key);
	}

	public function incr() {
		return $this->redis->incr($this->key);
	}

	public function incrBy($inc) {
		return $this->redis->incrBy($this->key, $inc);
	}

	public function decr() {
		return $this->redis->decr($this->key);
	}

	public function decrBy($inc) {
		return $this->redis->decrBy($this->key, $inc);
	}

	public function getSet($val) {
		return $this->redis->getSet($this->key, $val);
	}
}