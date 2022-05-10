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

use data_field_base;
/**
 * The Global User Report external api functions.
 *
 * @package     report_gur
 * @category    admin
 * @copyright   2022 Lukas Celinak, Edumood,  <lukascelinak@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace report_gur;

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");
require_once($CFG->dirroot . '/' . $CFG->admin . '/tool/dataprivacy/lib.php');

use coding_exception;
use context_helper;
use context_system;
use context_user;
use core\invalid_persistent_exception;
use core_user;
use dml_exception;
use external_api;
use external_description;
use external_function_parameters;
use external_multiple_structure;
use external_single_structure;
use external_value;
use external_warnings;
use invalid_parameter_exception;
use moodle_exception;
use required_capability_exception;
use restricted_context_exception;
use tool_dataprivacy\external\category_exporter;
use tool_dataprivacy\external\data_request_exporter;
use tool_dataprivacy\external\purpose_exporter;
use tool_dataprivacy\output\data_registry_page;

class external extends external_api
{
    public static function get_menu_parameters() {
        $menuParamater = new \external_value(
            PARAM_TEXT,
            'The menu level 1 parameter',
            VALUE_REQUIRED
        );
	    $contentId = new \external_value(
		    PARAM_INT,
		    'The menu level 1 parameter',
		    VALUE_REQUIRED
	    );

        $params = array(
            'level1' => $menuParamater,
	        'contentid'=>$contentId,
        );
        return new external_function_parameters($params);
    }

    public static function get_level2($level1,$contentid) {
	    global $PAGE;

	    $params = self::validate_parameters(
		    self::get_menu_parameters(),
		    array(
			    'level1' => $level1,
			    'contentid'=>$contentid,
		    )
	    );

        global $CFG, $DB;
	    $menulevel2=[];
	    $content = $DB->get_field('data_content', 'content', array('id'=>$params->contentid));
	    $convert_content = new \custom_menu($content, current_language());
		foreach ($convert_content->get_children() as $key => $menulvl1){
			if($menulvl1->get_text() == $params->$level1){
				foreach ($menulvl1->get_children() as $menulvl2){
					$menulevel2[].=$menulvl2->get_text();
				}
			}
		}


        return $menulevel2;
    }

    public static function get_courses_returns() {
        return new external_multiple_structure(
            new external_single_structure([
                'id'    => new external_value(PARAM_INT, 'ID of the course'),
                'fullname'  => new external_value(PARAM_NOTAGS, 'The course name')
            ])
        );
    }



}
