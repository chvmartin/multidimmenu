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
	    $menulevel1[].='Choose...';
	    foreach ($convert_content->get_children() as $key => $menulvl1) {
		    $menulevel1['firstlvl_'.$menulvl1->get_text()].=$menulvl1->get_text();
	    }
	    $autocomplete1 = new MoodleQuickForm_select('id_first_level', 'autocomplete lvl 1', $menulevel1, array('id'=>'id_first_level','contentid'=>$this->field->id));
	    $autocomplete2 = new MoodleQuickForm_select('id_second_level', 'autocomplete lvl 2', [], array('id'=>'id_second_level','contentid'=>$this->field->id,'disabled'=>'disabled'));
	    $autocomplete3 = new MoodleQuickForm_select('id_third_level', 'autocomplete lvl 3', [], array('id'=>'id_third_level','contentid'=>$this->field->id,'disabled'=>'disabled'));

	    $str .= html_writer::tag('input', '', array('name'=>'field_'.$this->field->id,'hidden'=>'true','id' => 'field_'.$this->field->id, 'class' => 'mod-data-input custom-select','value'=>$content));
	    $str .= html_writer::tag('p', $content);
	    $str .= html_writer::tag('br', '');
		$str .= "<div>" . $autocomplete1->toHtml() . "</div>";
	    $str .= "<div>" . $autocomplete2->toHtml() . "</div>";
	    $str .= "<div>" . $autocomplete3->toHtml() . "</div>";
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
}
