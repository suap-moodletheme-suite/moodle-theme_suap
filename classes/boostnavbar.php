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

namespace theme_suap;

use core\navigation\views\view;
use navigation_node;
use moodle_url;
use action_link;
use lang_string;
use html_writer;

/**
 * Creates a navbar for boost that allows easy control of the navbar items.
 *
 * @package    theme_suap
 * @copyright  2021 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class boostnavbar extends \theme_boost\boostnavbar {
    /**
     * Prepares the navigation nodes for use with boost.
     */
    protected function prepare_nodes_for_boost(): void {
        global $PAGE;
        // Remove the navbar nodes that already exist in the primary navigation menu.
        $this->remove_items_that_exist_in_navigation($PAGE->primarynav);

        // Defines whether section items with an action should be removed by default.
        $removesections = true;

        $this->breadcrumb_participants();

        if ($this->page->context->contextlevel == CONTEXT_USER) {
            $this->remove('courses');
            // Remove the course category breadcrumb nodes.
            foreach ($this->items as $key => $item) {
                // Remove if it is a course category breadcrumb node.
                $this->remove($item->key, \breadcrumb_navigation_node::TYPE_CATEGORY);
            }
        }
        if ($this->page->context->contextlevel == CONTEXT_COURSECAT) {
            // Remove the 'Permissions' navbar node in the Check permissions page.
            if ($this->page->pagetype === 'admin-roles-check') {
                $this->remove('permissions');
            }
        }
        if ($this->page->context->contextlevel == CONTEXT_COURSE) {
            // Remove any duplicate navbar nodes.
            $this->remove_duplicate_items();
            // Remove 'My courses' and 'Courses' if we are in the course context.
            $this->remove('mycourses');
            $this->remove('courses');
            // Remove the course category breadcrumb nodes.
            foreach ($this->items as $key => $item) {
                // Remove if it is a course category breadcrumb node.
                $this->remove($item->key, \breadcrumb_navigation_node::TYPE_CATEGORY);
            }

            // Remove the course breadcrumb node.
            if (!str_starts_with($this->page->pagetype, 'section-view-')) {
                // $this->remove($this->page->course->id, \breadcrumb_navigation_node::TYPE_COURSE);
            }

            // Remove the navbar nodes that already exist in the secondary navigation menu.
            $this->remove_items_that_exist_in_navigation($PAGE->secondarynav);

            switch ($this->page->pagetype) {
                case 'group-groupings':
                case 'group-grouping':
                case 'group-overview':
                case 'group-assign':
                    // Remove the 'Groups' navbar node in the Groupings, Grouping, group Overview and Assign pages.
                    $this->remove('groups');
                case 'backup-backup':
                case 'backup-restorefile':
                case 'backup-copy':
                case 'course-reset':
                    // Remove the 'Import' navbar node in the Backup, Restore, Copy course and Reset pages.
                    $this->remove('import');
                case 'course-user':
                    $this->remove('mygrades');
                    $this->remove('grades');
            }
        }

        // Remove 'My courses' if we are in the module context.
        if ($this->page->context->contextlevel == CONTEXT_MODULE) {
            $this->remove('mycourses');
            $this->remove('courses');
            // Remove the course category breadcrumb nodes.
            foreach ($this->items as $key => $item) {
                // Remove if it is a course category breadcrumb node.
                $this->remove($item->key, \breadcrumb_navigation_node::TYPE_CATEGORY);
            }
            $courseformat = course_get_format($this->page->course);
            $removesections = $courseformat->can_sections_be_removed_from_navigation();
            if ($removesections) {
                // If the course sections are removed, we need to add the anchor of current section to the Course.
                $coursenode = $this->get_item($this->page->course->id);
                if (!is_null($coursenode) && $this->page->cm->sectionnum !== null) {
                    $coursenode->action = course_get_format($this->page->course)->get_view_url($this->page->cm->sectionnum);
                }
            }
        }

        if ($this->page->context->contextlevel == CONTEXT_SYSTEM) {
            // Remove the navbar nodes that already exist in the secondary navigation menu.
            $this->remove_items_that_exist_in_navigation($PAGE->secondarynav);
        }

        // Set the designated one path for courses.
        $mycoursesnode = $this->get_item('mycourses');
        if (!is_null($mycoursesnode)) {
            $url = new \moodle_url('/my/courses.php');
            $mycoursesnode->action = $url;
            $mycoursesnode->text = get_string('mycourses');
        }

        $this->remove_no_link_items($removesections);

        // Don't display the navbar if there is only one item. Apparently this is bad UX design.
        // O if foi convertido para retirar o link do item quando for o único

        if ($this->item_count() <= 1) {
            // $this->clear_items();

            if (end($this->items)) {
                $this->remove_last_item_action();
            }

            return;
        }

        // Make sure that the last item is not a link. Not sure if this is always a good idea.
        $this->remove_last_item_action();
    }

    /**
     * Create breadcrumb for participants page. It's called in navbar.mustache
     */
    public function breadcrumb_participants() {
        if ($this->page->pagetype == 'course-view-participants') {
            $url = new \moodle_url('/course/view.php?id=' . $this->page->course->id);
            $shortname = $this->page->course->shortname;
            $link_participants = html_writer::tag('a', $shortname, ['href' => $url]);
            $breadcrumb_participants = html_writer::tag('li', $link_participants, ['class' => 'breadcrumb-item action-item']);
            return $breadcrumb_participants;
        }
    }
}
