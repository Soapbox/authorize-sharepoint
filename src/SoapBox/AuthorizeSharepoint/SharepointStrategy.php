<?php namespace SoapBox\AuthorizeSharepoint;

use SoapBox\Authorize\User;
use SoapBox\Authorize\Exceptions\AuthenticationException;
use SoapBox\Authorize\Strategies\SingleSignOnStrategy;
use Altek\SharePoint\SharePointException;
use Altek\SharePoint\RESTClient;

class SharepointStrategy extends SingleSignOnStrategy {

	/**
	 * An array of the permissions we require for the application.
	 */
	private $rc;
	private $configKey = 'key';
	private $soapboxUrl;

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
		$this->soapboxUrl = $settings['soapboxUrl'];

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

		Helpers::redirect($this->soapboxUrl);
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
			$remoteUser = $this->rc->getCurrentUserProfile($this->configKey);

			$user = new User;
			$user->id = $remoteUser['account'];
			$user->email =  $remoteUser['email'];
			$user->accessToken = $parameters['access_token'];
			$user->firstname = $remoteUser['name'];
			$user->lastname = '';//$remoteUser['account'];

			return $user;
		} catch (\Exception $e) {
			throw new AuthenticationException();
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
		$token = $parameters['access_token']; 

		//get the tokenFromUser
		$this->rc->tokenFromUser($this->configKey, $token);

		return $this->getUser($parameters);
	}

}
