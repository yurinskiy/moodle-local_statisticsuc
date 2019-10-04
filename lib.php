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
 * This file contains the code for the plugin integration.
 *
 * @package   local_statisticsuc
 * @copyright 2019, YuriyYurinskiy <yuriyyurinskiy@yandex.ru>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * To add the category and node information into the my profile page.
 *
 * @param $settingsnav
 * @param $context
 * @return void
 */
function local_statisticsuc_extend_settings_navigation($settingsnav, $context) {
    global $CFG, $PAGE;

    if (is_siteadmin() && $settingnode = $settingsnav->find('reports', navigation_node::TYPE_SETTING)) {
        $strfoo = get_string('pluginname','local_statisticsuc');
        $url = new moodle_url('/local/statisticsuc/index.php');
        $foonode = navigation_node::create(
                $strfoo,
                $url,
                navigation_node::TYPE_SETTING,
                'statisticsuc',
                'statisticsuc',
                new pix_icon('i/settings', $strfoo)
        );
        if ($PAGE->url->compare($url, URL_MATCH_BASE)) {
            $foonode->make_active();
        }
        $settingnode->add_node($foonode);
    }
}