<?php namespace Imvkmark\L5UploadClient\Helper;

use Curl\Curl;

class L5UploadClient {


	/**
	 * 获取上传token
	 * @return bool
	 * @throws \Exception
	 */
	public static function getUploadToken() {
		if (!config('l5-upload-client.enable')) {
			return '';
		}
		$timestamp = isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : 0;
		$version   = config('l5-upload-client.version', '1.0');
		$param     = [
			'app_key'   => config('l5-upload-client.app_key'),
			'timestamp' => $timestamp,
			'sign'      => self::calcSign($timestamp),
			'version'   => $version,
		];

		// 计算缓存过期
		$cacheKey    = self::cacheName(__CLASS__, 'token');
		$uploadToken = '';
		if (\Cache::has($cacheKey)) {
			$cacheToken = \Cache::get($cacheKey);
			if (isset($cacheToken['token']) && $cacheToken['expire_timestamp'] > $timestamp) {
				// 存在 token 且不过期
				$uploadToken = $cacheToken['token'];
			} else {
				\Cache::forget($cacheKey);
			}
		}
		if (!$uploadToken) {

			if (!config('l5-upload-client.token_url')) {
				throw new \Exception('Please set lemon picture server token url');
			} else {
				$curl   = new Curl();
				$upload = $curl->get(config('l5-upload-client.token_url'), $param);
				$upload = json_decode(json_encode($upload), true);
				if ($upload['status'] == 'success') {
					$uploadToken = $upload['data']['upload_token'];
					$expired     = (int) config('l5-upload-client.expires');
					\Cache::add($cacheKey, [
						'token'            => $uploadToken,
						'expire_timestamp' => $timestamp + $expired * 60,
					], config('l5-upload-client.expires'));
				} else {
					throw new \Exception($upload['msg']);
				}
			}

		}
		return $uploadToken;

	}

	private static function toKvStr($array) {
		$return = '';
		if (is_array($array)) {
			foreach ($array as $key => $value) {
				if (is_array($value)) {
					$return .= $key . '=' . self::toKvStr($value) . ',';
				} else {
					$return .= $key . '=' . $value . ',';
				}
			}
		} else {
			$return .= $array . ',';
		}
		return rtrim($return, ',');
	}

	/**
	 * 计算请求签名
	 * @param $timestamp
	 * @return string
	 */
	private static function calcSign($timestamp) {
		$array = [
			'timestamp'  => $timestamp,
			'app_key'    => config('l5-upload-client.app_key'),
			'app_secret' => config('l5-upload-client.app_secret'),
			'version'    => config('l5-upload-client.version', '1.0'),
		];
		ksort($array);
		$str = self::toKvStr($array);
		return sha1(md5($str));
	}

	/**
	 * 缓存前缀生成
	 * @param        $class
	 * @param string $suffix
	 * @return string
	 */
	private static function cacheName($class, $suffix = '') {
		$snake = str_replace('\\', '', snake_case(lcfirst($class)));
		return $suffix ? $snake . '_' . $suffix : $snake;
	}
}