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
 * Version details.
 *
 * @package    tool
 * @subpackage environment
 * @copyright  2018 Howard Miller
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);
define('CACHE_DISABLE_ALL', true);
require(__DIR__ . '/../../../../config.php');

require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/clilib.php');
require_once($CFG->libdir.'/environmentlib.php');

// Just the installed version.
$release = $CFG->version;

// Test environment first.
list($envstatus, $environmentresults) = check_moodle_environment(normalize_version($release), ENV_SELECT_RELEASE);

// Set up table.
$table = new \tool_environment\ConsoleTable();
$table->setHeaders([
    get_string('name', 'tool_environment'),
    get_string('information', 'tool_environment'),
    get_string('report', 'tool_environment'),
    get_string('status', 'tool_environment'),
]);

foreach ($environmentresults as $env) {
    $row = [];

    // Name.
    $row[] = "$env->part";

    // Information.
    $row[] = $env->info;

    // Report.
    if ($env->current_version) {
        $a = new stdClass();
        $a->current = $env->current_version;
        $a->needed = $env->needed_version;
        $row[] = get_string('version', 'tool_environment', $a);
    } else if ($env->part == 'php_setting') {
        if ($env->status) {
            $row[] = get_string('environmentsettingok', 'admin');
        } else if ($env->level == 'required') {
            $row[] = get_string('environmentmustfixsetting', 'admin');
        } else {
            $row[] = get_string('environmentshouldfixsetting', 'admin');
        }
    } else if ($env->part == 'custom_check') {
        if ($env->level == 'required') {
            $row[] = get_string('environmentrequirecustomcheck', 'admin');
        } else {
            $row[] = get_string('environmentrecommendcustomcheck', 'admin');
        }
    } else {
        if ($env->level == 'required') {
            $row[] = get_string('environmentrequireinstall', 'admin');
        } else {
            $row[] = get_string('environmentrecommendinstall', 'admin');
        }
    }

    // Status.
    if ($env->status) {
        $row[] = get_string('pass', 'tool_environment');
    } else {
        $row[] = get_string('fail', 'tool_environment');
    }

    $table->addRow($row);
}

$table->display();

