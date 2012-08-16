<?php
// This file is part of Moodle - http://moodle.org/.
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
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.

/**
 * @package    local
 * @subpackage profileswitches
 * @copyright 2012 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') or die;

/**
 * Adds module specific settings to the settings block
 *
 * @param settings_navigation $settings The settings navigation object
 * @param navigation_node $datanode The node to add module settings to
 */
function local_profileswitches_extends_navigation(global_navigation $navigation) {
    global $PAGE, $USER;

    if ($settingsnav = $PAGE->__get('settingsnav')) {
        $usersetting = $settingsnav->get('usercurrentsettings');
    }

    if (empty($usersetting)) {
        return;
    }
   
    $user = $USER;
    $systemcontext = get_system_context();

    if (isloggedin() && !isguestuser($user) && !is_mnet_remote_user($user)) {
        if ((is_siteadmin($user) || !is_siteadmin($user)) && has_capability('moodle/user:update', $systemcontext)) {
            
            $editor = $user->htmleditor ? 0 : 1;
            $streditor = $editor ? 'editoron' : 'editoroff';
            
            $switchparams = array('id' => $user->id, 'sesskey' => sesskey(), 'returnurl' => $PAGE->url->out(false));
            $switchurl = new moodle_url('/local/profileswitches/switch.php', $switchparams);

            // switch editor
            $url = new moodle_url($switchurl, array('editor' => $editor));
            $usersetting->add(get_string($streditor, 'local_profileswitches'), $url, $usersetting::TYPE_SETTING);
        }
    }

}
