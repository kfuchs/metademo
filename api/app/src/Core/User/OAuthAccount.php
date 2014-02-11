<?php namespace Core\User;

use Abstracts;
use Core\User;

class OAuthAccount extends Abstracts\Model {

	/**
	 * Settings and Metadata
	 */
	protected $table = 'user_oauth_accounts';

	const facebookType = 'facebook';

	public static $validTypes = ['facebook'];

	/**
	 * Relations
	 */
	public function user()
	{
		return $this->belongsTo(User::class, 'user_id');
	}

}