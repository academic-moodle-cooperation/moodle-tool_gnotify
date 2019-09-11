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


$template = $DB->get_record('gnotify_tpl', ['id'=> $id]);
if($template) {
    $templatelang = $DB->get_record('gnotify_tpl_lang', ['tplid'=> $template->id, 'lang' => 'en']);
    //TODO multilang
    $templatevars = $DB->get_fieldset_select('gnotify_tpl_var',  'varname' , 'tplid = :templateid', ['templateid' => $template->id]);
    //     $templatevars = ['var1','var2'];
    $templatecontext = array();
    $templatecontext['vars'] = $templatevars;
    $templatecontext['lang'] = $templatelang;
    $form = new tool_gnotify_use_form(new moodle_url('usetemplate.php',['templateid' => $id]) , $templatecontext);

    if ($form->is_cancelled()) {
        redirect(new moodle_url('/admin/tool/gnotify/templates.php'));
    } else if ($useform = $form->get_data()) {

        $trans = $DB->start_delegated_transaction();

        $record2 = new stdClass();
        $record2->tplid = $template->id;
        $record2->fromdate = $useform->fromdate;
        $record2->todate = $useform->todate;

        $id = $DB->insert_record('gnotify_tpl_ins', $record2);

        foreach($templatevars as $v) {
            $recordVar = new stdClass();
            $recordVar->insid = $id;
            $recordVar->varid = $DB->get_field('gnotify_tpl_var','id',['varname' => $v]);
            $recordVar->content = $useform->$v;
            $DB->insert_record('gnotify_tpl_ins_var', $recordVar);
        }


        $DB->commit_delegated_transaction($trans);
        redirect(new moodle_url('/admin/tool/gnotify/templates.php'));
    }

} else {
    print_error('wrongiderror','tool_gnotify');
}

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/admin/tool/gnotify/usetemplates.php?id=' . $id));
$PAGE->set_title(get_string('templates', 'tool_gnotify'));
$PAGE->set_pagelayout('admin');
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('templates', 'tool_gnotify'));
echo $form->render();
echo $OUTPUT->footer();