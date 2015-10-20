<?php

namespace OC\Share20;

use OCP\Files\Node;
use OCP\IUser;
use OCP\IGroup;

class Share {

	/** @var string */
	private $internalId;

	/** @var string */
	private $providerId;

	/** @var Node */
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

	/** @var string */
	private $password;

	/**
	 * Construct a new share object
	 *
	 * @param Node $path
	 * @param int $shareType
	 * @param IUser|IGroup|string $shareWith
	 * @param int $permissions
	 * @param \DateTime $expireDate
	 * @param string $password
	 * @param IUser|string $sharedBy
	 * @param IUser|string $shareOwner
	 * @param string $internalId
	 * @param string $providerId
	 */
	public function __construct(Node $path,
								$shareType,
								$shareWith,
								$permissions = 31,
								\DateTime $expireDate = null,
								$password = null,
								$sharedBy = null,
								$shareOwner = null,
								$internalId = null,
								$providerId = null) {
		$this->path = $path;
		$this->shareType = $shareType;
		$this->shareWith = $shareWith;

		$this->setPermissions($permissions);
		$this->setExpirationDate($expireDate);
		$this->setPassword($password);
		$this->setSharedBy($sharedBy);
		$this->setShareOwner($shareOwner);
		$this->setInternalId($internalId);
		$this->setProviderId($providerId);
	}

	/**
	 * Set the id of the ShareProvider
	 * Should only be used by the share manager
	 *
	 * @param string $providerId
	 * @return Share The modified object
	 */
	public function setProviderId($providerId) {
		$this->providerId = $providerId;
		return $this;
	}

	/**
	 * Set the internal (to the provider) share id
	 * Should only be used by the share provider
	 *
	 * @param string $id
	 * @return Share The modified object
	 */
	public function setInternalId($id) {
		$this->id = $id;
	}

	/**
	 * Get the internal (to the provider) share id
	 * Should only be used by the share provider
	 *
	 * @return string
	 */
	public function getInternalId() {
		return $this->internalId;
	}

	/**
	 * Get the id of the share
	 *
	 * @return string
	 */
	public function getId() {
		//TODO $id should be set as well as $providerId
		return $this->providerId . ':' . $this->id;
	}

	/**
	 * Get the path of this share for the current user
	 * 
	 * @return Node
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
		return $this->shareType;
	}

	/**
	 * Get the shareWith
	 *
	 * @return IUser|IGroup|string
	 */
	public function getShareWith() {
		return $this->shareWith;
	}

	/**
	 * Set the permissions
	 *
	 * @param int $permissions
	 * @return Share The modified object
	 */
	public function setPermissions($permissions) {
		//TODO checkes

		$this->permissions = $permissions;
		return $this;
	}

	/**
	 * Get the share permissions
	 *
	 * @return int
	 */
	public function getPermissions() {
		return $this->permissions;
	}

	/**
	 * Set the expiration date
	 *
	 * @param \DateTime $expireDate
	 * @return Share The modified object
	 */
	public function setExpirationDate(\DateTime $expireDate) {
		//TODO checks

		$this->expireDate = $expireDate;
		return $this;
	}

	/**
	 * Get the share expiration date
	 *
	 * @return \DateTime
	 */
	public function getExpirationDate() {
		return $this->expireDate;
	}

	/**
	 * Set the sharer of the path
	 *
	 * @param IUser|string $sharedBy
	 * @return Share The modified object
	 */
	public function setSharedBy($sharedBy) {
		//TODO checks
		$this->sharedBy = $sharedBy;

		return $this;
	}

	/**
	 * Get share sharer
	 *
	 * @return IUser|string
	 */
	public function getSharedBy() {
		//TODO check if set
		return $this->sharedBy;
	}

	/**
	 * Set the original share owner (who owns the path)
	 *
	 * @param IUser|string
	 *
	 * @return Share The modified object
	 */
	public function setShareOwner($shareOwner) {
		//TODO checks

		$this->shareOwner = $shareOwner;
		return $this;
	}

	/**
	 * Get the original share owner (who owns the path)
	 * 
	 * @return IUser|string
	 */
	public function getShareOwner() {
		//TODO check if set
		return $this->shareOwner;
	}

	/**
	 * Set the password
	 *
	 * @param string $password
	 *
	 * @return Share The modified object
	 */
	public function setPassword($password) {
		//TODO verify

		$this->password = $password;
		return $this;
	}
}
