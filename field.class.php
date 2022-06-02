<?php
///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999-onwards Moodle Pty Ltd  http://moodle.com          //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

class data_field_menucat extends data_field_base {

    var $type = 'menucat';
    /**
     * priority for globalsearch indexing
     *
     * @var int
     */
    protected static $priority = self::HIGH_PRIORITY;

    function display_add_field($recordid = 0, $formdata = null) {
        global $DB, $OUTPUT,$PAGE,$CFG;

        if ($formdata) {
            $fieldname = 'field_' . $this->  field->id;
            $content = $formdata->$fieldname;
        } else if ($recordid) {
            $content = $DB->get_field('data_content', 'content', array('fieldid'=>$this->field->id, 'recordid'=>$recordid));
            $content = trim($content);
        } else {
            $content = '';
        }

        $PAGE->requires->js_call_amd('datafield_menucat/moodle-datafield_menucat-form', 'init');

	    $str = '<div title="' . s($this->field->description) . '">';
	    $str .= '<label for="' . 'field_' . $this->field->id . '">';
	    $str .= html_writer::span($this->field->name, 'accesshide');
	    if ($this->field->required) {
		    $image = $OUTPUT->pix_icon('req', get_string('requiredelement', 'form'));
		    $str .= html_writer::div($image, 'inline-req');
	    }
	    $str .= '</label>';

	    $convert_content = new custom_menu($this->field->param1, current_language());
	    $menulevel1=[];
	    $menulevel2=[];
	    $menulevel3=[];
	    foreach ($convert_content->get_children() as $key => $menulvl1) {
		    $menulevel1['firstlvl_'.self::prepare_menu_item($menulvl1->get_text())].=$menulvl1->get_text();
		    if(json_decode($content)->firstlevel == $menulvl1->get_text()){
				foreach ($menulvl1->get_children() as $menulvl2){
					$menulevel2['secondlvl_'.self::prepare_menu_item($menulvl2->get_text())].=$menulvl2->get_text();
					if(json_decode($content)->secondlevel == $menulvl2->get_text()){
						foreach ($menulvl2->get_children() as $menulvl3){
							$menulevel3['thirdlvl_'.self::prepare_menu_item($menulvl3->get_text())].=$menulvl3->get_text();
						}
					}
				}

			}
	    }
	    $str .= html_writer::tag('input', '', array('name'=>'field_'.$this->field->id,'hidden'=>'true','id' => 'field_'.$this->field->id, 'class' => 'mod-data-input custom-select','value'=>$content));
	    $str .= html_writer::tag('label', 'Menu level 1:', array('name'=>'Menu level 1','value'=>'Menu level 1'));
		$str .= html_writer::select($menulevel1, 'id_first_level', 'firstlvl_'.self::prepare_menu_item(json_decode($content)->firstlevel), array('' => get_string('menuchoose', 'data')),
		    array('id' => 'id_first_level', 'class' => 'mod-data-input custom-select','contentid'=>$this->field->id));
	    $str .= html_writer::tag('label', 'Menu level 2:', array('name'=>'Menu level 2','value'=>'Menu level 2'));
	    $str .= html_writer::select($menulevel2, 'id_second_level', 'secondlvl_'.self::prepare_menu_item(json_decode($content)->secondlevel), array('' => get_string('menuchoose', 'data')),
		    array('id' => 'id_second_level', 'class' => 'mod-data-input custom-select','contentid'=>$this->field->id));
	    $str .= html_writer::tag('label', 'Menu level 3:', array('name'=>'Menu level 3','value'=>'Menu level 3'));
	    $str .= html_writer::select($menulevel3, 'id_third_level', 'thirdlvl_'.self::prepare_menu_item(json_decode($content)->thirdlevel), array('' => get_string('menuchoose', 'data')),
		    array('id' => 'id_third_level', 'class' => 'mod-data-input custom-select','contentid'=>$this->field->id));
	    $str .= '</div>';

        return $str;
    }

    function display_search_field($content = '') {
        global $CFG, $DB;

        $varcharcontent =  $DB->sql_compare_text('content', 255);
        $sql = "SELECT DISTINCT $varcharcontent AS content
                  FROM {data_content}
                 WHERE fieldid=? AND content IS NOT NULL";

        $usedoptions = array();
        if ($used = $DB->get_records_sql($sql, array($this->field->id))) {
            foreach ($used as $data) {
                $value = $data->content;
                if ($value === '') {
                    continue;
                }
                $usedoptions[$value] = $value;
            }
        }

        $options = array();
        foreach (explode("\n",$this->field->param1) as $option) {
            $option = trim($option);
            if (!isset($usedoptions[$option])) {
                continue;
            }
            $options[$option] = $option;
        }
        if (!$options) {
            // oh, nothing to search for
            return '';
        }

        $return = html_writer::label(get_string('fieldtypelabel', "datafield_" . $this->type),
            'menucatf_' . $this->field->id, false, array('class' => 'accesshide'));
        $return .= html_writer::select($options, 'f_'.$this->field->id, $content, array('' => get_string('menuchoose', 'data')),
                array('class' => 'custom-select'));
        return $return;
    }

    public function parse_search_field($defaults = null) {
        $param = 'f_'.$this->field->id;
        if (empty($defaults[$param])) {
            $defaults = array($param => '');
        }
        return optional_param($param, $defaults[$param], PARAM_NOTAGS);
    }

    function generate_sql($tablealias, $value) {
        global $DB;

        static $i=0;
        $i++;
        $name = "df_menucat_$i";
        $varcharcontent = $DB->sql_compare_text("{$tablealias}.content", 255);

        return array(" ({$tablealias}.fieldid = {$this->field->id} AND $varcharcontent = :$name) ", array($name=>$value));
    }

    /**
     * Check if a field from an add form is empty
     *
     * @param mixed $value
     * @param mixed $name
     * @return bool
     */
    function notemptyfield($value, $name) {
        return strval($value) !== '';
    }

    /**
     * Return the plugin configs for externallib functions.
     *
     * @return array the list of config parameters
     * @since Moodle 3.3
     */
    public function get_config_for_external() {
        // Return all the config parameters.
        $configs = [];
        for ($i = 1; $i <= 10; $i++) {
            $configs["param$i"] = $this->field->{"param$i"};
        }
        return $configs;
    }

    /**
     * Return prepared menuitem without whitespaces.
     *
     * @return string of prepared menuitem without whitespaces
     * @since Moodle 3.3
     */
    public function prepare_menu_item($value) {

        $menuitem = trim($value);
        $menuitem = str_replace(' ','_',$menuitem);
        return $menuitem;
    }

	/**
	 * Return prepared menuitem without whitespaces.
	 *
	 * @return string of prepared menuitem without whitespaces
	 * @since Moodle 3.3
	 */
	public function get_menu_items($menuitems) {

		$menuitem = trim($menuitems);
		$menuitem = str_replace(' ','_',$menuitem);
		return $menuitem;
	}
}
