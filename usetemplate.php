<?php
require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->libdir . '/adminlib.php');

require_login();
//TODO admin
$id = required_param('templateid', PARAM_INT);

admin_externalpage_setup('gnotify_templates');
global $DB;
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/admin/tool/gnotify/usetemplates.php?id=' . $id));
$PAGE->set_title(get_string('templates', 'tool_gnotify'));
$PAGE->set_pagelayout('admin');
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('templates', 'tool_gnotify'));
if(!$id) {
    echo get_string("noiderror","tool_gnotify");
}
$template = $DB->get_record('gnotify_tpl', ['id'=> $id]);
if($template) {
    $template = $DB->get_records('gnotify_tpl_lang', ['tplid'=> $template->id]);
    //TODO multilang
}
echo $OUTPUT->footer();