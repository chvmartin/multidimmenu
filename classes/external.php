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
 * The Global User Report externallib api functions.
 *
 * @package    datafield_menucat;
 * @category    admin
 * @copyright   2022 Lukas Celinak, Edumood,  <lukascelinak@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");


class datafield_menucat_external extends external_api
{
    public static function get_second_level_parameters() {
        $firstlevel = new \external_value(PARAM_TEXT, 'The menu level 1 parameter',);
	    $contentid = new \external_value(  PARAM_INT,'The field id', VALUE_REQUIRED);

        $params = array(
            'firstlevel' => $firstlevel,
	        'contentid'=>$contentid,
        );
        return new external_function_parameters($params);
    }

    public static function get_second_level($firstlevel,$contentid) {
	    global $PAGE;

	    $params = self::validate_parameters(
		    self::get_second_level_parameters(),
		    array(
			    'firstlevel' => $firstlevel,
			    'contentid'=>$contentid,
		    )
	    );

        global $DB;
	    $menusecondlevel=[];
	    $content = $DB->get_field('data_fields', 'param1', array('id'=>$params['contentid']));
	    $convert_content = new \custom_menu($content, current_language());
	    $firstlvlparam = substr($params['firstlevel'],strpos($params['firstlevel'],'_')+1,strlen($params['firstlevel']));
		foreach ($convert_content->get_children() as $key => $menufirstlevel){
			if($menufirstlevel->get_text() == $firstlvlparam){
				foreach ($menufirstlevel->get_children() as $child){
					$childprop = new \stdClass();
					$childprop->id = 'secondlvl_'.$child->get_text();
					$childprop->secondlevelitem = $child->get_text();
					$menusecondlevel[]=$childprop;
				}
			}
		}
       return $menusecondlevel;


    }

    public static function get_second_level_returns() {
        return new external_multiple_structure(
            new external_single_structure([
                'id'    => new external_value(PARAM_TEXT, 'ID of the course'),
                'secondlevelitem'  => new external_value(PARAM_TEXT, 'The course name')
            ])
        );
    }

	public static function get_third_level_parameters() {
		$firstlevel = new \external_value(PARAM_TEXT, 'The menu level 1 parameter',);
		$secondlevel = new \external_value(PARAM_TEXT, 'The menu level 2 parameter',);
		$contentid = new \external_value(  PARAM_INT,'The field id', VALUE_REQUIRED);

		$params = array(
			'firstlevel' => $firstlevel,
			'secondlevel' => $secondlevel,
			'contentid'=>$contentid,
		);
		return new external_function_parameters($params);
	}

	public static function get_third_level($firstlevel,$secondlevel,$contentid) {
		global $PAGE;

		$params = self::validate_parameters(
			self::get_third_level_parameters(),
			array(
				'firstlevel' => $firstlevel,
				'secondlevel' => $secondlevel,
				'contentid'=>$contentid,
			)
		);

		global $DB;
		$menuthirdlevel=[];
		$content = $DB->get_field('data_fields', 'param1', array('id'=>$params['contentid']));
		$convert_content = new \custom_menu($content, current_language());
		$secondlvlparam = substr($params['secondlevel'],strpos($params['secondlevel'],'_')+1,strlen($params['secondlevel']));
		foreach ($convert_content->get_children() as $key => $menufirstlevel){
			if($menufirstlevel->get_text() == trim($params['firstlevel'])){
				foreach ($menufirstlevel->get_children() as $key2 => $childsecondlevel){
					if($childsecondlevel->get_text() == $secondlvlparam){
						foreach ($childsecondlevel->get_children() as $child){

								$childprop = new \stdClass();
								$childprop->id = 'thirdlvl_'.$child->get_text();
								$childprop->thirdlevelitem = $child->get_text();
								$menuthirdlevel[]=$childprop;
						}

					}

				}
			}
		}
		return $menuthirdlevel;


	}

	public static function get_third_level_returns() {
		return new external_multiple_structure(
			new external_single_structure([
				'id'    => new external_value(PARAM_TEXT, 'ID of the course'),
				'thirdlevelitem'  => new external_value(PARAM_TEXT, 'The course name')
			])
		);
	}



}
