<?php

// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * The purpose of this file is to allow the user to turn off and on the editor
 * and js in the user's profile
 *
 * @package    local
 * @subpackage profileswitches
 * @copyright  2012 Itamar Tzadok
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

$id = required_param('id', PARAM_INT); // User id
$returnurl = required_param('returnurl', PARAM_URL);

$editor = optional_param('editor',-1, PARAM_INT);
$ajax = optional_param('ajax',-1, PARAM_INT);

$PAGE->set_url('/local/profileswitches/switch.php', array('id'=>$id));

if ($USER->id != $id) {
    print_error('invaliduserid');
}

if (!confirm_sesskey()) {
    print_error('confirmsesskeybad', 'error');
}

require_login();

$user = $USER;

$syscontext = get_system_context();
if (isloggedin() && !isguestuser($user) && !is_mnet_remote_user($user)) {
    if (is_siteadmin($user) || has_capability('moodle/user:editownprofile', $syscontext)) {

        if(isset($user->htmleditor) and $editor !=-1){
            $DB->set_field('user', 'htmleditor', $editor, array('id' => $id));
            $user->htmleditor = $editor;
        }else if($editor != -1){
            if($editor == 0){
                set_user_preference('htmleditor', 'textarea');
            }else if($editor == 1){
                unset_user_preference('htmleditor');
            }
        }

        if (isset($user->ajax) and $ajax != -1) {
            $DB->set_field('user', 'ajax', $ajax, array('id' => $id));
            $user->ajax = $ajax;
        } else if ($ajax != -1) {
            set_user_preference('courseajax', $ajax);
        }
    }
}

redirect($returnurl);
