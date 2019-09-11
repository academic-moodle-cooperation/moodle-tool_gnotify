<?php
require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->libdir . '/adminlib.php');

require_login();
//TODO admin

global $DB;

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/admin/tool/gnotify/edit.php?templateid=' . $id));

$id = required_param('templateid', PARAM_INT);
//admin_externalpage_setup('gnotify_edit');

//TODO multilang
$form = new tool_gnotify_edit_form(new moodle_url('edit.php', ['templateid' => $id]));
if ($form->is_cancelled()) {
    redirect(new moodle_url('/admin/tool/gnotify/templates.php'));
} else if ($useform = $form->get_data()) {

    $trans = $DB->start_delegated_transaction();

    $updateRecord = new stdClass();
    $updateRecord->id = $useform->langid;
    $updateRecord->content = $useform->content['text'];

    $DB->update_record('gnotify_tpl_lang', $updateRecord);

    preg_match_all('/{{\s*(.*?)\s*}}/', $useform->content['text'], $matches);

    foreach($matches[1] as $value) {
        if (!$DB->record_exists('gnotify_tpl_var', ['varname' => $value])) {
            $recordVar = new stdClass();
            $recordVar->tplid = $id;
            $recordVar->varname = $value;
            $DB->insert_record('gnotify_tpl_var', $recordVar);
        }
    }

    $DB->commit_delegated_transaction($trans);
    redirect(new moodle_url('/admin/tool/gnotify/templates.php'));
}

if ($templatelang = $DB->get_record('gnotify_tpl_lang', ['tplid' => $id])) {
    $template = $DB->get_record('gnotify_tpl', ['id' => $id]);
    $formdata['langid'] = $templatelang->id;
    $formdata['template_name'] = $template->name;
    $formdata['content']['text'] = $templatelang->content;
    $form->set_data($formdata);
}

$PAGE->set_title(get_string('templates', 'tool_gnotify'));
$PAGE->set_pagelayout('admin');

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('templates', 'tool_gnotify'));
echo $form->render();
echo $OUTPUT->footer();