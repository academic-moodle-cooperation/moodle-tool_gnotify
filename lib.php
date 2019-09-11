<?php
use mod_data\event\record_created;

class tool_gnotify_var_renderer extends renderer_base {
    
    public function render_direct($html,$vars) {
        $mustache = $this->get_mustache();
        $tmploader = $mustache->getLoader();
        $mustache->setLoader(new Mustache_Loader_StringLoader());
        $rendered = $this->render_from_template($html, $vars);
        $mustache->setLoader($tmploader);
        return $rendered;
        
    }
    
}
function tool_gnotify_before_footer() {
    
    global $PAGE, $DB,$USER;
    if (!isloggedin() || isguestuser()) {
        return;
    }
    if (in_array($PAGE->pagelayout, ['maintenance', 'print', 'redirect'])) {
        // Do not try to show notifications inside iframe, in maintenance mode,
        // when printing, or during redirects.
        return;
    }
    $sql= "SELECT g.id,l.content FROM {gnotify_tpl_ins} g, {gnotify_tpl_lang} l WHERE :time between fromdate AND todate AND l.lang = 'en' AND l.tplid = g.tplid AND NOT EXISTS( SELECT 1 FROM {gnotify_tpl_ins_ack} a WHERE g.id=a.insid AND a.userid = :userid)";
    $records = $DB->get_records_sql($sql,['time' => time(), 'userid' => $USER->id]);
    
    if($records) {
        $context = [];
        foreach ($records as $record) {
            $htmlcontent = format_text($record->content);
            $sql = "SELECT var.varname, content from {gnotify_tpl_ins_var} ins, {gnotify_tpl_var} var  WHERE var.id = ins.varid AND ins.insid = :insid";
            $vars = $DB->get_records_sql($sql,['insid' => $record->id]);
            $renderer = new tool_gnotify_var_renderer($PAGE, 'web');
            $vararray = [];
            foreach ($vars as $var) {
                $vararray[$var->varname] = $var->content;
            }
            $htmlcontent = $renderer->render_direct($htmlcontent,$vararray);
            
            $context['notifications'][] = ['html' => $htmlcontent ];
        }
        $PAGE->requires->js_call_amd('tool_gnotify/notification', 'init', $context);
    }
}

