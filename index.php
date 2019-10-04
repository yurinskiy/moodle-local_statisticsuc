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
 * @package   local_statisticsuc
 * @copyright 2019, YuriyYurinskiy <yuriyyurinskiy@yandex.ru>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->dirroot . '/user/filters/lib.php');
require('./locallib.php');

require_login();

$parent = optional_param('parent', 0, PARAM_INT);

$context = context_system::instance();

$url = new moodle_url('/local/statisticsuc/index.php', array('parent' => $parent));
if ($parent) {
    $DB->record_exists('course_categories', array('id' => $parent), '*', MUST_EXIST);
    $context = context_coursecat::instance($parent);
    $title = get_string('statisticcoursescat', 'local_statisticsuc',
            $DB->get_field('course_categories', 'name', array('id' => $parent)));
} else {
    $context = context_system::instance();
    $title = get_string('statisticcourses', 'local_statisticsuc');
}

$PAGE->set_pagelayout('admin');
$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_title(get_string('pluginname', 'local_statisticsuc'));

navigation_node::override_active_url(new moodle_url('/local/statisticsuc/index.php'), array('parent' => $parent));

$usersData[] = [
        get_string('users', 'local_statisticsuc') . $OUTPUT->help_icon('users', 'local_statisticsuc'),
        get_users(false)
];

$usersData[] = [
        get_string('userswithoutsuspended', 'local_statisticsuc') .
        $OUTPUT->help_icon('userswithoutsuspended', 'local_statisticsuc'),
        local_statisticsuc_count_users_suspended()
];

$usersData[] = [
        get_string('teachers', 'local_statisticsuc') . $OUTPUT->help_icon('teachers', 'local_statisticsuc'),
        local_statisticsuc_count_users_have_role(ROLE_TEACHER)
];

$usersData[] = [
        get_string('assistants', 'local_statisticsuc') . $OUTPUT->help_icon('assistants', 'local_statisticsuc'),
        local_statisticsuc_count_users_have_role(ROLE_ASSISTANT)
];
$usersData[] = [
        get_string('students', 'local_statisticsuc') . $OUTPUT->help_icon('students', 'local_statisticsuc'),
        local_statisticsuc_count_users_have_role(ROLE_STUDENT)
];

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('statisticusers', 'local_statisticsuc'));

$usersTable = new html_table();
$usersTable->head = [
        'Характеристика',
        'Значение'
];
$usersTable->align = [
        'left',
        'center',
];
$usersTable->size = [
        '80%',
        '20%',
];
$usersTable->id = 'statisticUser';
$usersTable->attributes['class'] = 'admintable generaltable';
$usersTable->data = $usersData;

echo html_writer::table($usersTable);

echo $OUTPUT->heading($title);

$options = array();
$options[0] = get_string('top');
$options += core_course_category::make_categories_list('moodle/category:manage');
$select = html_writer::select($options, 'parent', $parent, false, array('onchange' => 'this.form.submit()'));
$noscript = html_writer::tag('noscript', html_writer::tag('input', null, array(
        'type'  => 'submit',
        'name'  => 'submit',
        'value' => get_string('filter', 'local_statisticsuc')
)));
echo html_writer::tag('form', $select . $noscript, array('method' => 'get'));

$coursescount = local_statisticsuc_count_courses($parent);

$coursesData[] = [
        get_string('courses', 'local_statisticsuc') . $OUTPUT->help_icon('courses', 'local_statisticsuc'),
        $coursescount->all
];
$coursesData[] = [
        get_string('coursesvisible', 'local_statisticsuc') . $OUTPUT->help_icon('coursesvisible', 'local_statisticsuc'),
        $coursescount->visible
];
$coursesData[] = [
        get_string('courseshidden', 'local_statisticsuc') . $OUTPUT->help_icon('courseshidden', 'local_statisticsuc'),
        $coursescount->hidden
];

$coursesTable = new html_table();
$coursesTable->head = [
        get_string('parameter', 'local_statisticsuc'),
        get_string('value', 'local_statisticsuc')
];
$coursesTable->align = [
        'left',
        'center',
];
$coursesTable->size = [
        '80%',
        '20%',
];
$coursesTable->id = 'statisticUser';
$coursesTable->attributes['class'] = 'admintable generaltable';
$coursesTable->data = $coursesData;

echo html_writer::table($coursesTable);

echo $OUTPUT->footer();

?>