<?php

class RedisSet {
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

	public function add($col) {
		return $this->redis->sAdd($this->key, $col);
	}

	public function remove($col) {
		return $this->redis->sRem($this->key, $col);
	}

	public function isMember($col) {
		return $this->redis->sIsMember($this->key, $col);
	}

	public function size() {
		return $this->redis->sCard($this->key);
	}

	public function members() {
		return $this->redis->sMembers($this->key);
	}

	public function getRand() {
		return $this->redis->sRandMember($this->key);
	}
}