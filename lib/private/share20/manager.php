<?php

namespace OC\Share20;

use OCP\IUserManager;
use OCP\IGroupManager;
use OCP\IUser;

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

	public function __construct(IShareProvider $storageShareProvider,
	                            IShareProvider $federatedShareProvider,
	                            IUser $user,
	                            IUserManager $userManager,
	                            IGroupManager $groupManager) {
		$this->storageShareProvider = $storageShareProvider;
		$this->federatedShareProvider = $federatedShareProvider;
		$this->user = $user;
		$this->userManager = $userManager;
		$this->groupManager = $groupManager;
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
	 * @return Share
	 *
	 * @throws ShareNotFoundException
	 */
	public function getShareById($id) {
		$share = null;
		$found = false;

		try {
			$share = $this->storageShareProvider->getShareById($this->currentUser, $id);
		} catch (ShareNotFoundException $e) {

		}
		
		try {
			$share = $this->federatedShareProvider->getShareById($this->currentUser, $id);
		} catch (ShareNotFoundException $e) {
		}

		if ($found === false) {
			throw new ShareNotFoundException;
		}
		
		return $share;
	}

	/**
	 * Get all the shares for a given path
	 *
	 * @param \OCP\Files\Node $path
	 * @return [Share]
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
	 * @return [Share]
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
	 * @return Share
	 *
	 * @throws ShareNotFoundException
	 */
	public function getShareByToken($token) {
		$share = null;
		$found = false;

		try {
			$share = $this->storageShareProvider->getShareByToken($this->currentUser, $token);
		} catch (ShareNotFoundException $e) {

		}
		
		try {
			$share = $this->federatedShareProvider->getShareByToken($this->currentUser, $token);
		} catch (ShareNotFoundException $e) {
		}

		if ($found === false) {
			throw new ShareNotFoundException;
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
	 * @return array
	 */
	public function getAccessList(\OCP\Files\Node $path) {
	}
}
