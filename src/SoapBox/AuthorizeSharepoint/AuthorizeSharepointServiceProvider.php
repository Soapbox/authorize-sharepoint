<?php namespace SoapBox\AuthorizeSharepoint;

use Illuminate\Support\ServiceProvider;
use SoapBox\Authorize\StrategyFactory;

class AuthorizeSharepointServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('soapbox/authorize-sharepoint');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		StrategyFactory::register('sharepoint', 'SoapBox\AuthorizeSharepoint\SharepointStrategy');
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
