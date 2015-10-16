<?php

namespace OC\Share20;

use OC\Share20\Exceptions\ShareNotFoundException;

/**
 * This class is the communication hub for all sharing related operations.
 */
class Manager {


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
	 * Retrieve all share by the current user
	 *
	 * @return [Share]
	 */
	public function getShares() {
	}

	/**
	 * Retrieve a share by the share id
	 *
	 * @param int $id
	 * @return Share
	 *
	 * @throws ShareNotFoundException
	 */
	public function getShareById($id) {
	}

	/**
	 * Get all the shares for a given path
	 *
	 * @param \OCP\Files\Node $path
	 * @return [Share]
	 */
	public function getSharesByPath(\OCP\Files\Node $path) {
	}

	/**
	 * Get all shares that are shared with the current user
	 *
	 * @return [Share]
	 */
	public function getSharedWithMe($shareType = null) {
	}

	/**
	 * Get the share by token
	 *
	 * @param string $token
	 * @return Share
	 *
	 * @throws ShareNotFoundException
	 */
	public function getShareByToken($token) {
	}

}
