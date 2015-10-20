<?php

namespace OC\Share20;

use OCP\IUser;

interface IShareProvider {

	/**
	 * Share a path
	 * 
	 * @param Share $share
	 * @return Share The share object
	 */
	public function create(Share $share);

	/**
	 * Update a share
	 *
	 * @param Share $share
	 * @return Share The share object
	 */
	public function update(Share $share);

	/**
	 * Delete a share
	 *
	 * @param Share $share
	 */
	public function delete(Share $share);

	/**
	 * Get all shares by the given user
	 *
	 * @param IUser $user
	 */
	public function getShares(IUser $user);

	/**
	 * Get share by id
	 *
	 * @param int $id
	 */
	public function getShareById($id);

	/**
	 * Get shares for a given path
	 *
	 * @param \OCP\Files\Node $path
	 */
	public function getSharesByPath(\OCP\IUser $user, \OCP\Files\Node $path);

	/**
	 * Get shared with the given user
	 *
	 * @param IUser $user
	 * @param int $shareType
	 */
	public function getSharedWithMe(IUser $user, $shareType = null);

	/**
	 * Get a share by token and if present verify the password
	 *
	 * @param string $token
	 * @param string $password
	 */
	public function getShareByToken($token, $password = null);
}
