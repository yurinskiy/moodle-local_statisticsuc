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
 * This file contains helper code.
 *
 * @package   local_statisticsuc
 * @copyright 2019, YuriyYurinskiy <yuriyyurinskiy@yandex.ru>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

define('ROLE_TEACHER', 3);
define('ROLE_ASSISTANT', 4);
define('ROLE_STUDENT', 5);

/**
 * @param int $category
 * @return StdClass
 */
function local_statisticsuc_count_courses($category = 0) {
    global $DB;

    $result = new StdClass();

    if ($category == 0) {
        $result->visible = $DB->count_records('course', [
                        'visible' => 1
                ]) - 1;

        $result->hidden = $DB->count_records('course', [
                'visible' => 0
        ]);
    } else {
        $result->visible = $DB->count_records('course', [
                'visible'  => 1,
                'category' => $category
        ]);

        $result->hidden = $DB->count_records('course', [
                'visible'  => 0,
                'category' => $category
        ]);

        $records = $DB->get_records('course_categories', [
                'parent' => $category
        ]);

        while (count($records) > 0) {
            $record = array_shift($records);
            $result->visible += $DB->count_records('course', [
                    'visible'  => 1,
                    'category' => $record->id
            ]);

            $result->hidden += $DB->count_records('course', [
                    'visible'  => 0,
                    'category' => $record->id
            ]);
            $records += $DB->get_records('course_categories', [
                    'parent' => $record->id
            ]);
        }
    }

    $result->all = $result->visible + $result->hidden;

    return $result;
}

/**
 * @return int
 */
function local_statisticsuc_count_users_suspended() {
    $extrasql = 'suspended=:suspended';
    $params = [
            'suspended' => 0
    ];
    $result = get_users(false, '', false, null, "", '', '', '', '', '*', $extrasql, $params);

    if (is_numeric($result)) {
        return $result;
    }

    return 0;
}

/**
 * @param int $courserole
 * @return int
 */
function local_statisticsuc_count_users_have_role($courserole) {
    $extrasql = 'suspended=:suspended AND id IN (SELECT userid
                               FROM {role_assignments} a
                         INNER JOIN {context} b ON a.contextid=b.id
                         INNER JOIN {course} c ON b.instanceid=c.id
                              WHERE b.contextlevel=50 AND a.roleid = :courserole)';
    $params = [
            'suspended'  => 0,
            'courserole' => $courserole
    ];
    $result = get_users(false, '', false, null, "", '', '', '', '', '*', $extrasql, $params);

    if (is_numeric($result)) {
        return $result;
    }

    return 0;
}