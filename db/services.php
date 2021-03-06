<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * The Global User Report services.
 *
 * @package     report_gur
 * @category    admin
 * @copyright   2022 Lukas Celinak, Edumood,  <lukascelinak@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$functions = array(
	'datafield_multidimmenu_get_second_level' => array(
		'classname' => 'datafield_multidimmenu_external',
		'methodname' => 'get_second_level',
		'classpath' => '',
		'description'   => 'Load menu items',
		'type'          => 'write',
		'ajax'          => true,
		'capabilities'  => '',
		'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
	),
	'datafield_multidimmenu_get_third_level' => array(
		'classname' => 'datafield_multidimmenu_external',
		'methodname' => 'get_third_level',
		'classpath' => '',
		'description'   => 'Load menu items',
		'type'          => 'write',
		'ajax'          => true,
		'capabilities'  => '',
		'services'      => array(MOODLE_OFFICIAL_MOBILE_SERVICE)
	)
);
