<?php namespace Imvkmark\L5UploadClient;

use Illuminate\Support\ServiceProvider;

class L5UploadClientServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 * @var bool
	 */
	protected $defer = false;

	public function boot() {
		// 加载的时候进行配置项的发布
		$this->publishes([
			__DIR__ . '/../config/upload-client.php' => config_path('l5-upload-client.php'),
		], 'sour-lemon');
	}

	/**
	 * Register the service provider.
	 * @return void
	 */
	public function register() {
		// 配置文件合并
		$this->mergeConfigFrom(__DIR__ . '/../config/upload-client.php', 'l5-upload-client');
		
	}

	/**
	 * Get the services provided by the provider.
	 * @return array
	 */
	public function provides() {
		return [];
	}
}
