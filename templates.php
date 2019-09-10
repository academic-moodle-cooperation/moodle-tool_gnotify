<?php

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->libdir . '/adminlib.php');
admin_externalpage_setup('gnotify_templates');

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/admin/tool/gnotify/templates.php'));
$PAGE->set_title(get_string('templates', 'tool_gnotify'));
$PAGE->set_pagelayout('admin');
echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('templates', 'tool_gnotify'));

global $DB;
$templates = $DB->get_records('gnotify_tpl',null );
$templatestablecontext = array("templates" => $templates);
$templatestablecontext = array("templates" => [["name" => "t1", "id"=>"1"], 1 => ["name" => "t2", "id"=>"2"]]);
$templatestablecontext["wwwroot"] = $CFG->wwwroot;
$renderer = $PAGE->get_renderer('core');
echo $renderer->render_from_template('tool_gnotify/templates_table', $templatestablecontext);

echo $OUTPUT->footer();