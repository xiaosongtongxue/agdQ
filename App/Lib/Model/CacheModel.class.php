<?php
/**
 * 缓存模型 - 业务逻辑模型
 * @example
 * setType($type)						主动设置缓存类型
 * set($key, $value, $expire = null)	设置缓存key=>value，expire表示有效时间，null表示永久
 * get($key, $mutex = false)			获取缓存数据，支持mutex模式
 * getList($prefix, $key)				批量获取指定前缀下的多个key值的缓存
 * rm($key)								删除缓存
 * @author icubit <icubit@qq.com>
 * @version agdQ 1.0
 */
class CacheModel { 	  	
	protected $handler;						// 操作句柄
	protected $type = 'FILE';				// 缓存类型，默认为文件缓存
	
	/**
	 * 初始化缓存模型对象，缓存类型
	 * @return void
	 */
	public function __construct($type = '') {
		$type = D('Xdata')->get('cache_Config:cachetype');
		if($type == 'Memcache'){
			$setting = D('Xdata')->get('cache_Config:cachesetting');
			$memhost = explode(':',$setting);
			C('MEMCACHE_HOST',$memhost[0]);
			C('MEMCACHE_PORT',$memhost[1]);
		}
		!$type && $type = $this->type;
		$this->type = strtoupper($type);
		$this->handler = Cache::getInstance($type);
	}

	/**
	 * 链式设置缓存类型
	 * @param string $type 缓存类型
	 * @return object 缓存模型对象
	 */
	public function setType($type) {
		$this->type = strtoupper($type);
		$this->handler = Cache::getInstance($type);
		return $this;
	}

	/**
	 * 设置缓存
	 * @param string $key 缓存Key值
	 * @param mix $value 缓存Value值
	 * @param boolean 是否设置成功
	 */
	public function set($key,$value,$expire = null) {
		// 接管过期时间设置，-1表示永远不过期
		$value = array(
					'CacheData' => $value,
					'CacheMtime' => time(),
					'CacheExpire' => is_null($expire) ? '-1' : $expire
				);	
		//$key = C('DATA_CACHE_PREFIX').$key;
		return $this->handler->set($key,$value);
	}
	
	/**
	 * 获取缓存操作，支持mutex模式
	 * mutex使用注意
	 * 1.设置缓存（set）时，需要设置有效时间
	 * 2.获取缓存（get）时，需要主动创建缓存
	 * @param string $_key 缓存Key值
	 * @param boolean $mutex 是否启用mutex模式，默认为不启用
	 * @return mix 缓存数据
	 */
	public function get($_key, $mutex = false) {
		$key = $_key;
		
		// 获取缓存数据
		$data = $this->handler->get($key);

		// 未设置缓存 
		if(!$data) {
			return false;
		}
		
		// mutex模式未开启
		if (! $mutex) {
			if ($data ['CacheExpire'] < 0 || ($data ['CacheMtime'] + $data ['CacheExpire'] > time ())) {
				return $data['CacheData'];
			} else {
				// 过期，清理原始缓存
				$this->rm ( $_key );
				return false;
			}
		}
		
		// mutex模式开启
		if(($data['CacheMtime'] + $data['CacheExpire']) <= time()) {
			//正常情况，有过期时间设置的mutex模式
			if($data['CacheExpire'] > 0) {	
				$data['CacheMtime'] = time();
				$this->handler->set($key, $data);
				// 返回false，让调用程序去主动更新缓存
				return false;	
			} else {				
				//异常情况，没有设置有效期的时候，永久有效的时候
				if(!$data['CacheData']) {
					$this->rm($_key);
					return false;
				}
				return $data['CacheData'];
			}
		} else {
			return $data['CacheData'];
		}
	}
	
	/**
	 * 删除缓存
	 * @param string $_key 缓存Key值
	 * @return boolean 是否删除成功
	 */
	public function rm($_key) {
		$key  = $_key;
		return $this->handler->rm($key);
	}

	/**
	 * 清除缓存
	 * @access public
	 * @return boolen
	 */
	public function clear() {
		return $this->handler->clear();
	}

	/**
	 * 根据某个前缀，批量获取多个缓存
	 * @param string $prefix 缓存前缀
	 * @param string $key 缓存Key值
	 * @return mix 缓存数据
	 */
	public function getList($prefix, $key) {
		if($this->type == 'MEMCACHE') {
			// Memcache有批量获取缓存的接口
			$_data = $this->handler->getMulti($prefix, $key);
			foreach($_data as $k => $d) {
				$data[$k] = $this->_returnData($d['CacheData'], $key);
			}
		} else {
			foreach($key as $k) {
				$_k = $prefix.$k;
				$data[$k] = $this->get($_k);
			}
		}
		return $data;
	}
}
?>