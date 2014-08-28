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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package    local
 * @subpackage profileswitches
 * @copyright 2012 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') or die;

define('LPS_NO_EDITOR', 'textarea');

/**
 * Adds module specific settings to the settings block
 *
 * @param object $navigation navigation node
 */

function local_profileswitches_extends_settings_navigation(settings_navigation $nav, context $context) {
    global $PAGE, $USER;

    $usersetting = $nav->get('usercurrentsettings', navigation_node::TYPE_CONTAINER);
    $user = $USER;

    if (isloggedin() && !isguestuser($user) && !is_mnet_remote_user($user)) {
        if (is_siteadmin($user) || has_capability('moodle/user:editownprofile', context_system::instance())) {

            $switchparams = array('id' => $user->id, 'sesskey' => sesskey(), 'returnurl' => $PAGE->url->out(false));
            $switchurl = new moodle_url('/local/profileswitches/switch.php', $switchparams);

            // Switch editor.
            if (isset($user->htmleditor)) {
                $currenteditor = $user->htmleditor;
            } else {
                $currenteditor = get_user_preferences('htmleditor');
            }

            if ($currenteditor == LPS_NO_EDITOR) {
                $editor = 1;
                $streditor = 'editoron';
            } else {
                $editor = 0;
                $streditor = 'editoroff';
            }
            $node = $usersetting->add(get_string($streditor, 'local_profileswitches'),
                new moodle_url($switchurl, array('editor' => $editor)),
                navigation_node::TYPE_SETTING);

            // Switch course ajax in the era of profile ajax setting.
            if (isset($user->ajax)) {
                $currentajax = $user->ajax;
            } else {
                $currentajax = get_user_preferences('courseajax', 1);

                // Apply in course via the theme setting.
                $courseid = !empty($PAGE->course->id) ? $PAGE->course->id : 0;
                if ($courseid) {
                    $PAGE->theme->enablecourseajax = $currentajax;
                }
            }

            $ajax = $currentajax ? 0 : 1;
            $strajax = $ajax ? 'ajaxon' : 'ajaxoff';

            $url = new moodle_url($switchurl, array('ajax' => $ajax));
            $node = $usersetting->add(get_string($strajax, 'local_profileswitches'),
                new moodle_url($switchurl, array('ajax' => $ajax)),
                navigation_node::TYPE_SETTING);
        }
    }
}
