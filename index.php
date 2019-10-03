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

$context = context_system::instance();

$PAGE->set_pagelayout('admin');
$PAGE->set_context($context);
$PAGE->set_url('/local/statisticsuc/index.php', ['contextid' => $context->id]);
$PAGE->set_title(get_string('pluginname', 'local_statisticsuc'));

$data = [];

$usercount = get_users(false);
$data[] = [
        get_string('users', 'local_statisticsuc') . $OUTPUT->help_icon('users', 'local_statisticsuc'),
        $usercount
];

$data[] = [
        get_string('userswithoutsuspended', 'local_statisticsuc') .
        $OUTPUT->help_icon('userswithoutsuspended', 'local_statisticsuc'),
        local_statisticsuc_count_users_suspended()
];

$data[] = [
        get_string('teachers', 'local_statisticsuc') . $OUTPUT->help_icon('teachers', 'local_statisticsuc'),
        local_statisticsuc_count_users_have_role(ROLE_TEACHER)
];

$data[] = [
        get_string('assistants', 'local_statisticsuc') . $OUTPUT->help_icon('assistants', 'local_statisticsuc'),
        local_statisticsuc_count_users_have_role(ROLE_ASSISTANT)
];
$data[] = [
        get_string('students', 'local_statisticsuc') . $OUTPUT->help_icon('students', 'local_statisticsuc'),
        local_statisticsuc_count_users_have_role(ROLE_STUDENT)
];

$data[] = [
        '',
        ''
];

$coursescount = local_statisticsuc_count_courses();
$data[] = [
        get_string('courses', 'local_statisticsuc') . $OUTPUT->help_icon('courses', 'local_statisticsuc'),
        $coursescount->all
];
$data[] = [
        get_string('coursesvisible', 'local_statisticsuc') . $OUTPUT->help_icon('coursesvisible', 'local_statisticsuc'),
        $coursescount->visible
];
$data[] = [
        get_string('courseshidden', 'local_statisticsuc') . $OUTPUT->help_icon('courseshidden', 'local_statisticsuc'),
        $coursescount->hidden
];

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('pluginname', 'local_statisticsuc'));

$table = new html_table();
$table->head = [
        '',
        'Количество'
];
$table->colclasses = [
        'leftalign',
        'leftalign',
];
$table->id = 'statistic';
$table->attributes['class'] = 'admintable generaltable';
$table->data = $data;

echo html_writer::table($table);

echo $OUTPUT->footer();

?>