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
$templates = $DB->get_recordset('gnotify_tpl',null );

if ($templates->valid()) {
    $templatestablecontext = array("templates" => $templates);
}
$templatestablecontext["wwwroot"] = $CFG->wwwroot;

// Template ins
//TODO use moodle functions
$instemplates = $DB->get_records_sql('SELECT B.id, A.name, B.fromdate, B.todate FROM {gnotify_tpl} AS A RIGHT JOIN {gnotify_tpl_ins} as B ON A.id=B.tplid');

$readytpl = array();
foreach($instemplates as $value) {
    $fromdate = (new DateTime("@$value->fromdate"))->format('Y-m-d H:i:s');
    $todate = (new DateTime("@$value->todate"))->format('Y-m-d H:i:s');
    array_push($readytpl, ['name' => $value->name, 'fromdate' => $fromdate, 'todate' => $todate]);
}


$templatestablecontext['instemplates'] = $readytpl;

$renderer = $PAGE->get_renderer('core');
echo $renderer->render_from_template('tool_gnotify/templates_table', $templatestablecontext);

echo $OUTPUT->footer();