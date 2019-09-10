<?php

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->libdir . '/adminlib.php');



$context = context_system::instance();

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/admin/tool/gnotify/create_template.php'));
$PAGE->set_title(get_string('createtemplate', 'tool_gnotify'));
$PAGE->set_pagelayout('admin');

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('createtemplate', 'tool_gnotify'));

global $DB;

$renderer = $PAGE->get_renderer('core');
$cform = new tool_gnotify_create_form();

if ($cform->is_cancelled()) {
    redirect(new moodle_url('/admin/tool/gnotify/templates.php'));
} else if ($fromform = $cform->get_data()) {

    $trans = $DB->start_delegated_transaction();

    $record = new stdClass();
    $record->name = $fromform->template_name;
    $id = $DB->insert_record('gnotify_tpl', $record, true);

    $recordContent = new stdClass();
    $recordContent->tplid = $id;
    $recordContent->lang = 'en';
    $recordContent->content = $fromform->content['text'];
//TODO: lang should be not null
    $DB->insert_record('gnotify_tpl_lang', $recordContent);

    $DB->commit_delegated_transaction($trans);
    redirect(new moodle_url('/admin/tool/gnotify/templates.php'));
}

$params = ["cform" => $cform->render()];

echo $renderer->render_from_template('tool_gnotify/create_template', $params);

echo $OUTPUT->footer();