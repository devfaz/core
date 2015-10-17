<?php

namespace OC\Share20;

use OCP\IUser;
use OCP\IGroup;
use OCP\Files\Node;

class share {

	/** @var IShareProvider */
	private $provider;

	/** @var int */
	private $id;

	/** @var Node $path */
	private $path;

	/** @var int */
	private $shareType;

	/** @var IUser|IGroup|string */
	private $shareWith;

	/** @var IUser|string */
	private $sharedBy;

	/** @var IUser|string */
	private $shareOwner;

	/** @var int */
	private $permissions;

	/** @var \DateTime */
	private $expireDate;

	public function __construct(IShareProvider $provider,
	                            $id,
	                            \OCP\Files\Node $path,
	                            $shareType,
	                            $shareWith,
	                            $sharedBy,
	                            $shareOwner,
	                            $permissions,
	                            \DateTime $expireDate = null) {
	}


	/**
	 * Get the id
	 *
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Get the path this share points to
	 *
	 * @return \OCP\Files\Node
	 */
	public function getPath() {
		return $this->path;
	}

	/**
	 * Get the shareType
	 *
	 * @return int
	 */
	public function getShareType() {
		return $this->shareType();
	}

	/**
	 * Get the receiver
	 *
	 * @return \OCP\IUser | \OCP\IGroup | string
	 */
	public function getShareWith() {
		return $this->shareWith();
	}

	/**
	 * Get the user that shared
	 *
	 * @return \OCP\IUser
	 */
	public function getSharedBy() {
		return $this->sharedBy();
	}

	/**
	 * Get the user that own the share
	 *
	 * @return \OCP\IUser
	 */
	public function getShareOwner() {
		return $this->shareOwner;
	}

	/**
	 * Get the permissions
	 *
	 * @return int
	 */
	public function getPermissions() {
		return $this->permissions;
	}

	/**
	 * Set the permissions
	 *
	 * @param int $permissions
	 */
	public function setPermissions($permissions) {
		//TODO sanity checks
		$this->provider->setSharePermissions($this->id, $permissions);
	}

	/**
	 * Get the expiration date
	 *
	 * @return \DateTime
	 */
	public function getExpirationDate() {
		return $this->expireDate;
	}

	/**
	 * Set the expiration date
	 *
	 * @param \DateTime $expireDate
	 */
	public function setExpirationDate(\DateTime $expireDate) {
		$this->provider->setShareExpirationDate($this->id, $expireDate);
	}

	/**
	 * Verify password
	 *
	 * @param string $password
	 * @return bool
	 */
	public function verifyPassword($password) {
		return $this->provider->verifySharePassword($this->id, $password);
	}

	/**
	 * Set password
	 *
	 * @param string password
	 */
	public function setPassword($password) {
		$this->provider->setSharePassword($this->id, $password);
	}
	
	/**
	 * Accept the share
	 */
	public function accept() {
		$this->provider->acceptShare($this->id);
	}

	/**
	 * Reject the share
	 */
	public function reject() {
		$this->provider->rejectShare($this->id);
	}

	/**
	 * Delete the share
	 */
	public function delete() {
		$this->provider->deleteShare($this->id);
	}
}
