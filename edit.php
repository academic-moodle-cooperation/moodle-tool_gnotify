<?php
require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->libdir . '/adminlib.php');

require_login();
//TODO admin
$id = required_param('templateid', PARAM_INT);
//admin_externalpage_setup('gnotify_edit');
global $DB;
$context = context_system::instance();

//TODO multilang
$form = new tool_gnotify_edit_form(new moodle_url('edit.php', ['templateid' => $id]));
if ($form->is_cancelled()) {
    redirect(new moodle_url('/admin/tool/gnotify/templates.php'));
} else if ($useform = $form->get_data()) {

    $trans = $DB->start_delegated_transaction();

    $updateRecord = new stdClass();
    $updateRecord->id = $useform->langid;
    $updateRecord->content = $useform->content['text'];

    $id = $DB->update_record('gnotify_tpl_lang', $updateRecord);

    $DB->commit_delegated_transaction($trans);
    redirect(new moodle_url('/admin/tool/gnotify/templates.php'));
}
if ($templatelang = $DB->get_record_sql('SELECT A.id, A.name, B.id as langid, B.lang, B.content FROM {gnotify_tpl} as A LEFT JOIN {gnotify_tpl_lang} as B ON A.id = B.tplid')) {

    $formdata['langid'] = $templatelang->langid;
    $formdata['template_name'] = $templatelang->name;
    $formdata['content']['text'] = $templatelang->content;
    $form->set_data($formdata);
}
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/admin/tool/gnotify/edit.php?templateid=' . $id));
$PAGE->set_title(get_string('templates', 'tool_gnotify'));
$PAGE->set_pagelayout('admin');

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('templates', 'tool_gnotify'));
echo $form->render();
echo $OUTPUT->footer();