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

require_once('../../config.php');
require_once('./form.php');
require_once('./locallib.php');
require_once('./render.php');

require_login();

$parent = optional_param('parent', 0, PARAM_INT);

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

/** Проверяем права пользователя */
if (!is_siteadmin() && !has_capability('local/statisticsuc:view', $context)) {
    header("Location: " . $CFG->wwwroot);
    die();
}

$PAGE->set_pagelayout('admin');
$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_title(get_string('pluginname', 'local_statisticsuc'));

navigation_node::override_active_url(new moodle_url('/local/statisticsuc/index.php'), array('parent' => $parent));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('statisticusers', 'local_statisticsuc'));

echo local_statisicsuc_render_info_sites();

echo $OUTPUT->heading($title);

$mform = new local_statisticsuc_form(null, array('parent' => $parent));
$mform->display();

echo local_statisticsuc_render_info_courses($parent);

echo $OUTPUT->footer();

?>