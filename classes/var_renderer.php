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
 * Render  in Moodle directly from strings
 *
 * @package     tool_gnotify
 * @author      Philipp Hager <philipp.hager@edaktik.at>
 * @copyright   2019 University of Vienna {@link http://www.univie.ac.at}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Class tool_gnotify_var_renderer
 *
 * @copyright   2019 University of Vienna {@link http://www.univie.ac.at}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_gnotify_var_renderer extends renderer_base {

    /**
     * Render direct
     *
     * @param string $html Template
     * @param array $vars Variables
     * @return bool|string
     * @throws moodle_exception
     */
    public function render_direct($html, $vars) {
        $mustache = $this->get_mustache();
        $tmploader = $mustache->getLoader();
        $mustache->setLoader(new Mustache_Loader_StringLoader());
        $rendered = $this->render_from_template($html, $vars);
        $mustache->setLoader($tmploader);
        return $rendered;

    }

}
