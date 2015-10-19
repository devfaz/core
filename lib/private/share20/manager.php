<?php

namespace OC\Share20;

use OCP\IUserManager;
use OCP\IGroupManager;
use OCP\IUser;
use OCP\ILogger;

use OC\Share20\Exceptions\ShareNotFoundException;

/**
 * This class is the communication hub for all sharing related operations.
 */
class Manager {

	/**
	 * Share provider for user, group and link shares
	 * @var IShareProvider
	 */
	private $storageShareProvider

	/**
	 * Share provider for federated shares
	 * @var ISharePrvider
	 */
	private $federatedShareProvider

	/** @var IUser */
	private $currentUser;

	/** @var IUserManager */
	private $userManager;

	/** @var IGroupManager */
	private $groupManager;

	/** @var ILogger */
	private $logger;

	public function __construct(IShareProvider $storageShareProvider,
	                            IShareProvider $federatedShareProvider,
	                            IUser $user,
	                            IUserManager $userManager,
	                            IGroupManager $groupManager,
								ILogger $logger) {
		$this->storageShareProvider = $storageShareProvider;
		$this->federatedShareProvider = $federatedShareProvider;
		$this->user = $user;
		$this->userManager = $userManager;
		$this->groupManager = $groupManager;
		$this->logger = $logger;
	}

	/**
	 * Share a path
	 * 
	 * @param \OCP\Files\Node $path
	 * @param int $shareType
	 * @param \OCP\IUser|\OCP\IGroup|string $shareWith
	 * @param int $permissions
	 * @param \DateTime $expireDate
	 * @param $password
	 */
	public function share(\OCP\Files\Node $path,
	                      $shareType,
	                      $shareWith,
	                      $permissions = 31,
	                      \DateTime $expireDate = null,
	                      $password = null) {

		//TODO some path checkes?

		//TODO check shareWith

		//TODO check for sane permissions

		/* 
		 * TODO first sanity expiredate validations
		 * So no dates in past. Sanitize date (so no time)
		 * Globally enforced dates
		 */

		/*
		 * TODO Verify password strength etc
		 */

		if ($shareType === \OC\Share\Constants::SHARE_TYPE_USER ||
		    $shareType === \OC\Share\Constants::SHARE_TYPE_GROUP ||
		    $shareType === \OC\Share\Constants::SHARE_TYPE_LINK) {
			$share = $this->storageShareProvider($path, $shareType, $shareWith, $permissions, $expireDate, $password);			
		} else if ($shareType === \OC\Share\Constants::SHARE_TYPE_REMOTE) {
			$share = $this->federatedShareProvider($path, $shareType, $shareWith, $permissions, $expireDate, $password);
		} else {
			//TODO: EXCEPTION not a valid share type
		}

		return $share;
	}

	/**
	 * Retrieve all share by the current user
	 */
	public function getShares() {
		$storageShares = $this->storageShareProvider->getShares($this->currentUser);
		$federatedShares = $this->federatedShareProvider->getShares($this->currentUser);

		//TODO: ID's should be unique who handles this?

		$shares = array_merge($storageShares, $federatedShares);
		return $shares;
	}

	/**
	 * Retrieve a share by the share id
	 *
	 * @param int $id
	 *
	 * @throws ShareNotFoundException
	 */
	public function getShareById($id) {
		//TODO some magic to deterine which provider is responsible for the id
		$provider = getShareProvider($id);

		try {
			$share = $provider->storageShareProvider->getShareById($this->currentUser, $id);
		} catch (ShareNotFoundException $e) {
			//TODO: Some error handling?
			throw new ShareNotFoundException();
		}
		
		return $share;
	}

	/**
	 * Get all the shares for a given path
	 *
	 * @param \OCP\Files\Node $path
	 */
	public function getSharesByPath(\OCP\Files\Node $path) {
		$storageShares = $this->storageShareProvider->getSharesByPath($this->currentUser, $path);
		$federatedShares = $this->federatedShareProvider->getSharesByPath($this->currentUser, $path);

		//TODO: ID's should be unique who handles this?

		$shares = array_merge($storageShares, $federatedShares);
		return $shares;
	}

	/**
	 * Get all shares that are shared with the current user
	 *
	 * @param int $shareType
	 */
	public function getSharedWithMe($shareType = null) {
		$storageShares = $this->storageShareProvider->getSharedWithMe($this->currentUser, $shareType);
		$federatedShares = $this->federatedShareProvider->getSharedWithMe($this->currentUser, $shareType);

		//TODO: ID's should be unique who handles this?

		$shares = array_merge($storageShares, $federatedShares);
		return $shares;
	}

	/**
	 * Get the share by token
	 *
	 * @param string $token
	 *
	 * @throws ShareNotFoundException
	 */
	public function getShareByToken($token) {
		// Only link shares have tokens and they are handeld by the storageShareProvider
		try {
			$share = $this->storageShareProvider->getShareByToken($this->currentUser, $token);
		} catch (ShareNotFoundException $e) {
			// TODO some error handling
			throw new ShareNotFoundException();
		}
		
		return $share;
	}

	/**
	 * Get access list to a path. This means
	 * all the users and groups that can access a given path.
	 *
	 * Consider:
	 * -root
	 * |-folder1
	 *  |-folder2
	 *   |-fileA
	 *
	 * fileA is shared with user1
	 * folder2 is shared with group2
	 * folder1 is shared with user2
	 *
	 * Then the access list will to '/folder1/folder2/fileA' is:
	 * [
	 * 	'users' => ['user1', 'user2'],
	 *  'groups' => ['group2']
	 * ]
	 *
	 * This is required for encryption
	 *
	 * @param \OCP\Files\Node $path
	 */
	public function getAccessList(\OCP\Files\Node $path) {
	}

	/**
	 * Set permissions of share
	 *
	 * @param int $id
	 * @param int $permissions
	 */
	public function setPermissions($id, $permissions) {
		//TODO check permissions sainity
	}

	/**
	 * Set expiration date of share
	 *
	 * @param int $id
	 * @param \DateTime $expireDate
	 */
	public function setExpirationDate($id, \DateTime $expireDate) {
		//TODO Date sanitation
	}

	/**
	 * Verify password of share
	 *
	 * @param int $id
	 * @param string $password
	 */
	public function verifyPassword($id, $password) {

	}

	/**
	 * Set password of share
	 *
	 * @param int $id
	 * @param string $password
	 */
	public function setPassword($id, $password) {

	}

	/**
	 * Accept a share
	 *
	 * @param int $id
	 */
	public function accept($id) {
	}

	/**
	 * Reject a share
	 *
	 * @param int $id
	 */
	public function reject($id) {

	}

	/**
	 * Delete a share
	 *
	 * @param int $id
	 */
	public function delete($id) {
	}

	/**
	 * Get the share provider based on the share id
	 *
	 * @param int $id
	 * @return IShareProvider
	 */
	private function getShareProvider($id) {

	}
}
