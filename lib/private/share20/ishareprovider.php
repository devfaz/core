<?php

namespace OC\Share20;

use OCP\IUser;

interface IShareProvider {

	/**
	 * Share a path
	 * 
	 * @param \OCP\Files\Node $path
	 * @param int $shareType
	 * @param \OCP\IUser|\OCP\IGroup|string $shareWith
	 * @param int $permissions
	 * @param \DateTime $expireDate
	 * @param $password
	 *
	 * @return Share
	 */
	public function share(\OCP\Files\Node $path,
	                      $shareType,
	                      $shareWith,
	                      $permissions = 31,
	                      \DateTime $expireDate = null,
	                      $password = null) {
	}

	/**
	 * Get all shares by the given user
	 *
	 * @param IUser $user
	 * @return [Shares]
	 */
	public function getShares(IUser $user);

	/**
	 * Get share by id
	 *
	 * @param int $id
	 * @return Share
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
	 * Get a share by token
	 *
	 * @param string $token
	 */
	public function getShareByToken($token);


	/**
	 * Set the expiration date
	 *
	 * @param int $id
	 * @param \DateTime $expireDate
	 */
	public function setShareExpirationDate($id, \DateTime $expireDate);

	/**
	 * Set permissions
	 *
	 * @param int $id
	 * @param int $permissions
	 */
	public function setSharePermissions($id, $permissions);

	/**
	 * Set password
	 *
	 * @param int $id
	 * @param string $password
	 */
	public function setPassword($id, $password);

	/**
	 * Verify the password
	 *
	 * @param int $id
	 * @param string $password
	 */
	public function verifyPassword($id, $password);

	/**
	 * Accept a share
	 *
	 * @param int $id
	 */
	public function acceptShare($id);

	/**
	 * Reject a share
	 *
	 * @param int $id
	 */
	public function rejectShare($id);

	/**
	 * Delete a share
	 * 
	 * @param int $id
	 */
	public function deleteShare($id);
}
