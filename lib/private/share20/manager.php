<?php

namespace OC\Share20;


use OCP\IAppConfig
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
	 * @var IShareProvider[]
	 */
	private $shareProviders;

	/**
	 * @var IShareProver[]
	 */
	private $shareTypeToShareProvider;

	/** @var IUser */
	private $currentUser;

	/** @var IUserManager */
	private $userManager;

	/** @var IGroupManager */
	private $groupManager;

	/** @var ILogger */
	private $logger;

	/** @var IAppConfig */
	private $appConfig;

	public function __construct(IUser $user,
	                            IUserManager $userManager,
	                            IGroupManager $groupManager,
								ILogger $logger,
								IAppConfig $appConfig) {
		$this->user = $user;
		$this->userManager = $userManager;
		$this->groupManager = $groupManager;
		$this->logger = $logger;
		$this->appConfig = $appConfig;
	}

	/**
	 * Registers a callback function which much return a IShareProvider
	 *
	 * @param string $id
	 * @param string $displayName
	 * @param int[] $shareTypes
	 * @param callacble $callBack
	 */
	public function registerShareProvider($id, $displayName, $shareTypes, callable $callBack) {
		if (isset($this->shareProviders[$id])) {
			//THROW EXCEPTION
		}

		$this->shareProviders[$id] = [
			'id' => $id,
			'displayName' => $displayName,
			'shareTypes' => $shareTypes,
			'callback' => $callBack,
		];

		foreach ($shareTypes as $shareType) {
			if (isset($this->shareTypeToShareProvider[$shareType])) {
				//THROW EXCEPTION
			}
			$this->shareTypeToShareProvider[$shareType] = $id;
		}
	}

	/**
	 * Get a ShareProvider
	 *
	 * @param string $id
	 * @return IShareProvider
	 */
	private function getShareProvider($id) {
		if (!isset($this->shareProviders)) {
			//Throw exception
		}

		$provider = call_user_func($this->shareProviders[$id]['callback']);
		if (!($provider instanceof \OC\Share20\IShareProvider)) {
			//Throw exception
		}

		return $provider;
	}

	/**
	 * Get shareProvider based on shareType
	 *
	 * @param int $shareType
	 * @return IShareProvider
	 */
	private function getShareProviderByType($shareType) {
		if (!isset($this->shareTypeToShareProvider[$shareType])) {
			//Throw exception
		}

		return $this->getShareProvider($this->shareTypeToShareProvider[$shareType]);
	}

	/**
	 * Share a path
	 * 
	 * @param string $path
	 * @param int $shareType
	 * @param string $shareWith
	 * @param int $permissions
	 * @param \DateTime $expireDate
	 * @param $password
	 */
	public function share($path,
	                      $shareType,
	                      $shareWith,
	                      $permissions = 31,
	                      \DateTime $expireDate = null,
	                      $password = null) {

		//TODO some path checkes
		//Convert to Node etc

		/*
		 * Basic sanity checks for the $shareType and $shareWith
		 */
		if ($shareType === \OC\Share\Constants::SHARE_TYPE_USER) {
			if (!$this->userManager->userExists($shareWith)) {
				//TODO Exception time
			}
		} else if ($shareType === \OC\Share\Constants::SHARE_TYPE_GROUP) {
			if (!$this->groupManager->groupExists($shareWith)) {
				//TODO Exception time
			}

		} else if ($shareType === \OC\Share\Constants::SHARE_TYPE_LINK) {
			// here sharewith is just an alias (could be e-mail?)
		} else if ($shareType === \OC\Share\Constants::SHARE_TYPE_REMOTE) {
			//Verify that $shareWith is a valid remote addess
		} else {
			//TODO Exception time
		}

		//TODO check for sane permissions
		if ($permissions & \OCP\Constants::PERMISSION_READ === 0) {
			//TODO Exception all shares require read access
		} else {
			//TODO Verify permissions make sense
			// e.g. Shares of a file can't have delete permissions etc
		}

		/* 
		 * TODO first sanity expiredate validations
		 * So no dates in past. Sanitize date (so no time)
		 * Globally enforced dates
		 */
		if ($expireDate !== null) {
			// We don't care about time
			$expireDate->setTime(0,0,0);

			$currentDate = new \DateTime();
			$currentDate->setTime(0,0,0);

			// Expiredate can't be in the past
			if ($expireDate <= $currentDate) {
				//TODO Expcetion time
			}

			//TODO Check enfroced expiration
		}


		/*
		 * TODO Verify password strength etc
		 */

		$provider = $this->getShareProviderByType($shareType);
		$share = $provider->share($path, $shareType, $shareWith, $permissions, $expireDate, $password);

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
	 * @param string $id
	 *
	 * @throws ShareNotFoundException
	 */
	public function getShareById($id) {
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
	 * @param string $id
	 * @param int $permissions
	 */
	public function setPermissions($id, $permissions) {
		//TODO check permissions sainity
	}

	/**
	 * Set expiration date of share
	 *
	 * @param string $id
	 * @param \DateTime $expireDate
	 */
	public function setExpirationDate($id, \DateTime $expireDate) {
		//TODO Date sanitation
	}

	/**
	 * Verify password of share
	 *
	 * @param string $id
	 * @param string $password
	 */
	public function verifyPassword($id, $password) {

	}

	/**
	 * Set password of share
	 *
	 * @param string $id
	 * @param string $password
	 */
	public function setPassword($id, $password) {

	}

	/**
	 * Accept a share
	 *
	 * @param string $id
	 */
	public function accept($id) {
	}

	/**
	 * Reject a share
	 *
	 * @param string $id
	 */
	public function reject($id) {

	}

	/**
	 * Delete a share
	 *
	 * @param string $id
	 */
	public function delete($id) {
	}

	/**
	 * Verify that all the required fields are present
	 *
	 * @param mixed[] $share
	 * @return mixed[]
	 */
	private function verifyShare($share) {
	}

	/**
	 * Format a share properly
	 * 	permissions => int
	 *  expireDate => ISO 8601 date
	 *
	 * @param mixed[] $share
	 */
	private function formatShare($share) {
	}

}
