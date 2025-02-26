<?php
/**
 * @author Bart Visscher <bartv@thisnet.nl>
 * @author Morris Jobke <hey@morrisjobke.de>
 * @author Robin McCorkell <rmccorkell@karoshi.org.uk>
 * @author Roeland Jago Douma <roeland@famdouma.nl>
 * @author Thomas Müller <thomas.mueller@tmit.eu>
 * @author Tom Needham <tom@owncloud.com>
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

class OC_OCS_Cloud {

	public static function getCapabilities() {
		$result = array();
		list($major, $minor, $micro) = OC_Util::getVersion();
		$result['version'] = array(
			'major' => $major,
			'minor' => $minor,
			'micro' => $micro,
			'string' => OC_Util::getVersionString(),
			'edition' => OC_Util::getEditionString(),
			);
			
		$result['capabilities'] = \OC::$server->getCapabilitiesManager()->getCapabilities();

		return new OC_OCS_Result($result);
	}
	
	public static function getCurrentUser() {
		$email=\OC::$server->getConfig()->getUserValue(OC_User::getUser(), 'settings', 'email', '');
		$data  = array(
			'id' => OC_User::getUser(),
			'display-name' => OC_User::getDisplayName(),
			'email' => $email,
		);
		return new OC_OCS_Result($data);
	}
}
