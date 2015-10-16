<?php

namespace OC\Share20;

class share {
	
	/**
	 * Get the id
	 *
	 * @return int
	 */
	public function getId() {
	}

	/**
	 * Get the path this share points to
	 *
	 * @return \OCP\Files\Node
	 */
	public function getPath() {
	}

	/**
	 * Get the shareType
	 *
	 * @return int
	 */
	public function getShareType() {
	}

	/**
	 * Get the receiver
	 *
	 * @return \OCP\IUser | \OCP\IGroup | string
	 */
	public function getShareWith() {
	}

	/**
	 * Get the user that shared
	 *
	 * @return \OCP\IUser
	 */
	public function getSharedBy() {
	}

	/**
	 * Get the user that own the share
	 *
	 * @return \OCP\IUser
	 */
	public function getShareOwner() {
	}

	/**
	 * Get the permissions
	 *
	 * @return int
	 */
	public function getPermissions() {
	}

	/**
	 * Set the permissions
	 *
	 * @param int $permissions
	 */
	public function setPermissions($permissions) {
	}

	/**
	 * Get the expiration date
	 *
	 * @return \DateTime
	 */
	public function getExpirationDate() {
	}

	/**
	 * Set the expiration date
	 *
	 * @param \DateTime $expireDate
	 */
	public function setExpirationDate(\DateTime $expireDate) {
	}

	/**
	 * Verify password
	 *
	 * @param string $password
	 * @return bool
	 */
	public function verifyPassword($password) {
	}

	/**
	 * Set password
	 *
	 * @param string password
	 */
	public function setPassword($password) {
	}
	
	/**
	 * Accept the share
	 */
	public function accept() {
	}

	/**
	 * Reject the share
	 */
	public function reject() {
	}

	/**
	 * Delete the share
	 */
	public function delete() {
	}
}
