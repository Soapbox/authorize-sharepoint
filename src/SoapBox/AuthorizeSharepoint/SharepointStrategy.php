<?php namespace SoapBox\AuthorizeSharepoint;

use SoapBox\Authorize\User;
use SoapBox\Authorize\Exceptions\AuthenticationException;
use SoapBox\Authorize\Exceptions\MissingArgumentsException;
use SoapBox\Authorize\Strategies\SingleSignOnStrategy;
use SoapBox\SharePoint\SharePointException;
use SoapBox\SharePoint\RESTClient;

class SharepointStrategy extends SingleSignOnStrategy {

	/**
	 * An array of the permissions we require for the application.
	 */
	private $rc;
	private $configKey = 'key';

	/**
	 * Initializes the Sharepoint Authentication with our id and secret
	 *
	 * @param array $settings [
	 *		'application_name' => string,
	 *		'id' => string,
	 *		'secret' => string,
	 *		'redirect_url' => string,
	 *		'developer_key' => string
	 *	]
	 * @param callable $store A callback that will store a KVP (Key Value Pair).
	 * @param callable $load A callback that will return a value stored with the
	 *	provided key.
	 */
	public function __construct($settings = array(), $store = null, $load = null) {
		if( !isset($settings['url']) ||
			!isset($settings['acs']) ||
			!isset($settings['client_id']) ||
			!isset($settings['secret']) ) {
			throw new MissingArgumentsException(
				'Required parameters url, path, acs, client_id, or secret are missing'
			);
		}

		$config = [
			$this->configKey => [
				'url'       => $settings['url'],
				'path'      => '/',//'/sites/mySite',
				'acs'       => $settings['acs'],
				'client_id' => $settings['client_id'],
				'secret'    => $settings['secret'],
			],
		];

		$this->rc = new RESTClient($config);
	}

	/**
	 * Used to authenticate our user through one of the various methods.
	 *
	 * @param array parameters array('access_token' => string,
	 *	'redirect_url' => string)
	 *
	 * @throws AuthenticationException If the provided parameters do not
	 *	successfully authenticate.
	 *
	 * @return User A mixed array repreesnting the authenticated user.
	 */
	public function login($parameters = array()) {
		return $this->getUser($parameters);
	}

	/**
	 * Used to retrieve the user from the strategy.
	 *
	 * @param array parameters The parameters required to authenticate against
	 *	this strategy. (i.e. accessToken)
	 *
	 * @throws AuthenticationException If the provided parameters do not
	 *	successfully authenticate.
	 *
	 * @return User A mixed array representing the authenticated user.
	 */
	public function getUser($parameters = array()) {
		try {
			$this->rc->tokenFromUser($this->configKey, $parameters['access_token']);
			$remoteUser = $this->rc->getCurrentUserProfile($this->configKey);

			if( preg_match("/st0([a-z0-9]+)@/i", $remoteUser['email']) ) {
				throw new AuthenticationException('Please sign in with your personal account.');
			}

			$user = new User;
			$user->id = $remoteUser['account'];
			$user->email =  $remoteUser['email'];
			$user->accessToken = 'token';
			$name = explode(' ', $remoteUser['name'], 2);
			$user->firstname = $name[0];
			$user->lastname = $name[1];

			return $user;
		} catch (\Exception $e) {
			throw new AuthenticationException(null, 0, $e);
		}

	}

	/**
	 * Used to handle tasks after login. This could include retrieving our users
	 * token after a successful authentication.
	 *
	 * @return array Mixed array of the tokens and other components that
	 *	validate our user.
	 */
	public function endpoint($parameters = array()) {
		return $this->getUser($parameters);
	}

}
