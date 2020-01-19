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

define('CONTEXT_COURSECAT', 40);
define('CONTEXT_COURSE', 50);

/**
 * @param int $category
 * @return StdClass
 */
function local_statisticsuc_count_courses($category = 0)
{
    global $DB;

    $result = new StdClass();

    if (0 === (int)$category) {
        $result->visible = $DB->count_records('course', [
                'visible' => 1
            ]) - 1;

        $result->hidden = $DB->count_records('course', [
            'visible' => 0
        ]);

        $result->teacher = local_statisticsuc_count_users_have_role(ROLE_TEACHER);
        $result->assistant = local_statisticsuc_count_users_have_role(ROLE_ASSISTANT);
        $result->student = local_statisticsuc_count_users_have_role(ROLE_STUDENT);

    } else {
        $result->visible = $DB->count_records('course', [
            'visible' => 1,
            'category' => $category
        ]);

        $result->hidden = $DB->count_records('course', [
            'visible' => 0,
            'category' => $category
        ]);

        $result->teacher = local_statisticsuc_users_have_role(ROLE_TEACHER, $category);
        $result->assistant = local_statisticsuc_users_have_role(ROLE_ASSISTANT, $category);
        $result->student = local_statisticsuc_users_have_role(ROLE_STUDENT, $category);

        $records = $DB->get_records('course_categories', [
            'parent' => $category
        ]);

        while (count($records) > 0) {
            $record = array_shift($records);
            $result->visible += $DB->count_records('course', [
                'visible' => 1,
                'category' => $record->id
            ]);

            $result->hidden += $DB->count_records('course', [
                'visible' => 0,
                'category' => $record->id
            ]);

            $result->teacher = merge($result->teacher, local_statisticsuc_users_have_role(ROLE_TEACHER, $record->id));
            $result->assistant = merge($result->assistant, local_statisticsuc_users_have_role(ROLE_ASSISTANT, $record->id));
            $result->student = merge($result->student, local_statisticsuc_users_have_role(ROLE_STUDENT, $record->id));

            $records += $DB->get_records('course_categories', [
                'parent' => $record->id
            ]);
        }

        $result->teacher = count(array_unique($result->teacher));
        $result->assistant = count(array_unique($result->assistant));
        $result->student = count(array_unique($result->student));
    }

    $result->all = $result->visible + $result->hidden;

    return $result;
}

/**
 * Возвращает количество учетных записей
 *
 * @param bool $withSuspended - если FALSE, то считает только незаблокированных пользователей
 * @return int
 */
function local_statisticsuc_count_users($withSuspended = false)
{
    global $DB;
    $sql = 'SELECT COUNT(*) FROM {user} u WHERE u.username <> \'guest\'';
    $params = [];

    if (!$withSuspended) {
        $sql .= ' AND u.suspended=:suspended';

        $params['suspended'] = 0;
    }

    $result = $DB->count_records_sql($sql, $params);

    if (is_numeric($result)) {
        return $result;
    }

    return 0;
}

/**
 * @param int $courserole
 * @return int
 */
function local_statisticsuc_count_users_have_role($courserole)
{
    global $DB;
    $sql = '
SELECT COUNT(DISTINCT u.id) 
  FROM {user} u 
 WHERE u.username <> \'guest\'
   AND u.suspended = :suspended 
   AND EXISTS(
       SELECT 1 userid
         FROM {role_assignments} a
   INNER JOIN {context} b ON a.contextid=b.id
   INNER JOIN {course} c ON b.instanceid=c.id
        WHERE a.userid = u.id 
          AND b.contextlevel = :contextlevel
          AND a.roleid = :courserole
        LIMIT 1
   )';

    $params = [
        'suspended' => 0,
        'contextlevel' => CONTEXT_COURSE,
        'courserole' => $courserole
    ];

    $result = $DB->count_records_sql($sql, $params);

    if (is_numeric($result)) {
        return $result;
    }

    return 0;
}

/**
 * @param int $courserole
 * @return array
 */
function local_statisticsuc_users_have_role($courserole, $category)
{
    global $DB;
    $sql = '
SELECT DISTINCT u.id
  FROM {user} u 
 WHERE u.username <> \'guest\'
   AND u.suspended = :suspended 
   AND EXISTS(
       SELECT 1 userid
         FROM {role_assignments} a
   INNER JOIN {context} b ON a.contextid=b.id
   INNER JOIN {course} c ON b.instanceid=c.id
   INNER JOIN {course_categories} cc ON cc.id = c.category AND cc.id = :category
        WHERE a.userid = u.id 
          AND b.contextlevel = :contextlevel
          AND a.roleid = :courserole
        LIMIT 1
   )';

    $params = [
        'suspended' => 0,
        'contextlevel' => CONTEXT_COURSE,
        'courserole' => $courserole,
        'category' => $category
    ];

    $result = $DB->get_records_sql($sql, $params);

    return array_map(function($a) {return  $a->id;}, $result);
}

function merge($a, $b){
    foreach($b as $arr){
        $a[]=$arr;
    }
    return $a;
}