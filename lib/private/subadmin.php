<?php
/**
 * @author Bart Visscher <bartv@thisnet.nl>
 * @author Georg Ehrke <georg@owncloud.com>
 * @author Lukas Reschke <lukas@owncloud.com>
 * @author Morris Jobke <hey@morrisjobke.de>
 * @author Roeland Jago Douma <roeland@famdouma.nl>
 * @author Thomas Müller <thomas.mueller@tmit.eu>
 *
 * @copyright Copyright (c) 2015, ownCloud, Inc.
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

namespace OC;

use OC\Hooks\PublicEmitter;
use OCP\IUser;
use OCP\IUserManager;
use OCP\IGroup;
use OCP\IGroupManager;
use OCP\IDBConnection;

class SubAdmin extends PublicEmitter {

	/** @var IUserManager */
	private $userManager;

	/** @var IGroupManager */
	private $groupManager;

	/** @var IDBConnection */
	private $dbConn;

	public function __construct(IUserManager $userManager,
	                            IGroupManager $groupManager,
								IDBConnection $dbConn) {
		$this->userManager = $userManager;
		$this->groupManager = $groupManager;
		$this->dbConn = $dbConn;

		$this->userManager->listen('\OC\User', 'postDelete', function($user) {
			$this->post_deleteUser($user);
		});
		$this->groupManager->listen('\OC\Group', 'postDelete', function($group) {
			$this->post_deleteGroup($group);	
		});
	}

	/**
	 * add a SubAdmin
	 * @param IUser $user user to be SubAdmin
	 * @param IGroup $group group $user becomes subadmin of
	 * @return bool
	 */
	public function createSubAdmin(IUser $user, IGroup $group) {
		$qb = $this->dbConn->getQueryBuilder();

		$result = $qb->insert('group_admin')
			->values([
				'gid' => $qb->createNamedParameter($group->getGID()),
				'uid' => $qb->createNamedParameter($user->getUID())
			])
			->execute();

		$this->emit('\OC\SubAdmin', 'postCreateSubAdmin', [$user, $group]);
		\OC_Hook::emit("OC_SubAdmin", "post_createSubAdmin", ["gid" => $group->getGID()]);
		return true;
	}

	/**
	 * delete a SubAdmin
	 * @param IUser $user the user that is the SubAdmin
	 * @param IGroup $group the group
	 * @return bool
	 */
	public function deleteSubAdmin(IUser $user, IGroup $group) {
		$qb = $this->dbConn->getQueryBuilder();

		$result = $qb->delete('group_admin')
			->where($qb->expr()->eq('gid', $qb->createNamedParameter($group->getGID())))
			->andWhere($qb->expr()->eq('uid', $qb->createNamedParameter($user->getUID())))
			->execute();

		$this->emit('\OC\SubAdmin', 'postDeleteSubAdmin', [$user, $group]);
		\OC_Hook::emit("OC_SubAdmin", "post_deleteSubAdmin", ["gid" => $group->getGID()]);
		return true;
	}

	/**
	 * get groups of a SubAdmin
	 * @param IUser $user the SubAdmin
	 * @return IGroup[]
	 */
	public function getSubAdminsGroups(IUser $user) {
		$qb = $this->dbConn->getQueryBuilder();

		$result = $qb->select('gid')
			->from('group_admin')
			->where($qb->expr()->eq('uid', $qb->createNamedParameter($user->getUID())))
			->execute();

		$groups = [];
		while($row = $result->fetch()) {
			$groups[] = $this->groupManager->get($row['gid']);
		}

		return $groups;
	}

	/**
	 * get SubAdmins of a group
	 * @param IGroup $group the group
	 * @return IUser[]
	 */
	public function getGroupsSubAdmins(IGroup $group) {
		$qb = $this->dbConn->getQueryBuilder();

		$result = $qb->select('uid')
			->from('group_admin')
			->where($qb->expr()->eq('gid', $qb->createNamedParameter($group->getGID())))
			->execute();

		$users = [];
		while($row = $result->fetch()) {
			$users[] = $this->userManager->get($row['uid']);
		}

		return $users;
	}

	/**
	 * get all SubAdmins
	 * @return array
	 */
	public function getAllSubAdmins() {
		$qb = $this->dbConn->getQueryBuilder();

		$result = $qb->select('*')
			->from('group_admin')
			->execute();

		$subadmins = [];
		while($row = $result->fetch()) {
			$subadmins[] = [
				'user'  => $this->userManager->get($row['uid']),
				'group' => $this->groupManager->get($row['gid'])
			];
		}
		return $subadmins;
	}

	/**
	 * checks if a user is a SubAdmin of a group
	 * @param IUser $user 
	 * @param IGroup $group
	 * @return bool
	 */
	public function isSubAdminofGroup(IUser $user, IGroup $group) {
		$qb = $this->dbConn->getQueryBuilder();

		$result = $qb->select('*')
			->from('group_admin')
			->where($qb->expr()->eq('gid', $qb->createNamedParameter($group->getGID())))
			->andWhere($qb->expr()->eq('uid', $qb->createNamedParameter($user->getUID())))
			->execute();

		return !empty($result->fetch()) ? true : false;
	}

	/**
	 * checks if a user is a SubAdmin
	 * @param IUser $user 
	 * @return bool
	 */
	public function isSubAdmin(IUser $user) {
		// Check if the user is already an admin
		if ($this->groupManager->isAdmin($user->getUID())) {
			return true;
		}

		$qb = $this->dbConn->getQueryBuilder();

		$result = $qb->select('gid')
			->from('group_admin')
			->andWhere($qb->expr()->eq('uid', $qb->createNamedParameter($user->getUID())))
			->setMaxResults(1)
			->execute()
			->fetch();

		return $result === false ? false : true;
	}

	/**
	 * checks if a user is a accessible by a subadmin
	 * @param IUser $subadmin
	 * @param IUser $user
	 * @return bool
	 */
	public function isUserAccessible($subadmin, $user) {
		if(!$this->isSubAdmin($subadmin)) {
			return false;
		}
		if($this->groupManager->isAdmin($user->getUID())) {
			return false;
		}
		$accessiblegroups = $this->getSubAdminsGroups($subadmin);
		foreach($accessiblegroups as $accessiblegroup) {
			if($accessiblegroup->inGroup($user)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * delete all SubAdmins by $user
	 * @param IUser $user
	 * @return boolean
	 */
	private function post_deleteUser($user) {
		$qb = $this->dbConn->getQueryBuilder();

		$result = $qb->delete('group_admin')
			->where($qb->expr()->eq('uid', $qb->createNamedParameter($user->getUID())))
			->execute();

		return true;
	}

	/**
	 * delete all SubAdmins by $group
	 * @param IGroup $group
	 * @return boolean
	 */
	private function post_deleteGroup($group) {
		$qb = $this->dbConn->getQueryBuilder();

		$result = $qb->delete('group_admin')
			->where($qb->expr()->eq('gid', $qb->createNamedParameter($group->getGID())))
			->execute();

		return true;
	}
}
