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
 * This file contains render code.
 *
 * @package   local_statisticsuc
 * @copyright 2019, YuriyYurinskiy <yuriyyurinskiy@yandex.ru>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Генерирует таблицу информации о категории
 *
 * @param int $category
 * @return string
 * @throws coding_exception
 * @throws dml_exception
 */
function local_statisticsuc_render_info_courses($category = 0)
{
    global $OUTPUT;

    $coursescount = local_statisticsuc_count_courses($category);

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

    $data[] = [
        get_string('teachers', 'local_statisticsuc') . $OUTPUT->help_icon('teachers', 'local_statisticsuc'),
        $coursescount->teacher ?? 0
    ];

    $data[] = [
        get_string('assistants', 'local_statisticsuc') . $OUTPUT->help_icon('assistants', 'local_statisticsuc'),
        $coursescount->assistant ?? 0
    ];
    $data[] = [
        get_string('students', 'local_statisticsuc') . $OUTPUT->help_icon('students', 'local_statisticsuc'),
        $coursescount->student ?? 0
    ];

    return local_statisicsuc_render_table('courses', $data);
}

/**
 * Генерирует таблицу информации о сайте
 *
 * @return string
 * @throws coding_exception
 */
function local_statisicsuc_render_info_sites()
{
    global $OUTPUT;

    $data[] = [
        get_string('users', 'local_statisticsuc') . $OUTPUT->help_icon('users', 'local_statisticsuc'),
        local_statisticsuc_count_users(true)
    ];

    $data[] = [
        get_string('userswithoutsuspended', 'local_statisticsuc') .
        $OUTPUT->help_icon('userswithoutsuspended', 'local_statisticsuc'),
        local_statisticsuc_count_users(false)
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

    return local_statisicsuc_render_table('users', $data);
}

/**
 * Формирует таблицу для вывода информации
 *
 * @param $id
 * @param $data
 * @return string
 * @throws coding_exception
 */
function local_statisicsuc_render_table($id, $data)
{
    $table = new html_table();
    $table->head = [
        get_string('parameter', 'local_statisticsuc'),
        get_string('value', 'local_statisticsuc')
    ];
    $table->align = [
        'left',
        'center',
    ];
    $table->size = [
        '80%',
        '20%',
    ];
    $table->id = $id;
    $table->attributes['class'] = 'admintable generaltable';
    $table->data = $data;

    return html_writer::table($table);
}