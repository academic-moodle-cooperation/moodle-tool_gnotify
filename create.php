<?php

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->libdir . '/adminlib.php');

global $DB;

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/admin/tool/gnotify/create.php'));
require_login();
//TODO admin

$cform = new tool_gnotify_create_form();

if ($cform->is_cancelled()) {
    redirect(new moodle_url('/admin/tool/gnotify/templates.php'));
} else if ($fromform = $cform->get_data()) {

    $trans = $DB->start_delegated_transaction();

    $record = new stdClass();
    $record->name = $fromform->template_name;
    $id = $DB->insert_record('gnotify_tpl', $record);

    $recordContent = new stdClass();
    $recordContent->tplid = $id;
    $recordContent->lang = 'en';
    $recordContent->content = $fromform->content['text'];
    //TODO: lang should be not null
    $DB->insert_record('gnotify_tpl_lang', $recordContent);

    preg_match_all('/{{\s*(.*?)\s*}}/', $fromform->content['text'], $matches);

    foreach($matches[1] as $value) {
        $recordVar = new stdClass();
        $recordVar->tplid = $id;
        $recordVar->varname = $value;
        $DB->insert_record('gnotify_tpl_var', $recordVar);
    }

    $DB->commit_delegated_transaction($trans);
    redirect(new moodle_url('/admin/tool/gnotify/templates.php'));
}

$PAGE->set_title(get_string('createtemplate', 'tool_gnotify'));
$PAGE->set_pagelayout('admin');

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('createtemplate', 'tool_gnotify'));
echo $cform->render();
echo $OUTPUT->footer();