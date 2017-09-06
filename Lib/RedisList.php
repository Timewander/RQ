<?php

class RedisList {
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
		return $this->redis->rPush($this->key, $col);
	}

	public function adds($cols) {
		return $this->redis->rPushX($this->key, $cols);
	}

	public function cheat($col) {
		return $this->redis->lPush($this->key, $col);
	}

	public function cheats($cols) {
		return $this->redis->lPushX($this->key, $cols);
	}

	public function pop() {
		return $this->redis->lPop($this->key);
	}

	public function cancel() {
		return $this->redis->rPop($this->key);
	}

	public function size() {
		return $this->redis->lLen($this->key);
	}

	public function remove($val, $count) {
		return $this->redis->lRem($this->key, $val, $count);
	}

	public function head() {
		return $this->redis->lIndex($this->key, 0);
	}

	public function tail() {
		return $this->redis->lIndex($this->key, -1);
	}

	public function all() {
		return $this->redis->lRange($this->key, 0, -1);
	}
}