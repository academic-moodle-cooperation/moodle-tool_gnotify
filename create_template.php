<?php

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->libdir . '/adminlib.php');



$context = context_system::instance();

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/admin/tool/gnotify/create_template.php'));
$PAGE->set_title(get_string('create_template', 'tool_gnotify'));
$PAGE->set_pagelayout('admin');

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('create_template', 'tool_gnotify'));

global $DB;

$renderer = $PAGE->get_renderer('core');
$cform = new tool_gnotify_create_form('create_template.php',[],'post');
$params = ["cform" => $cform->render()];


echo $renderer->render_from_template('tool_gnotify/create_template', $params);

echo $OUTPUT->footer();