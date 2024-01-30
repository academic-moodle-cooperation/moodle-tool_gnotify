Global Notifications aka gnotify
================================

Template support
----------------

The template variables must be matched by the following regex. `/{{\s*([a-zA-Z0-9_]+?)\s*}}/`.
```
Moodle maintenance will start on {{ main_date }} at {{ start }} and end at {{ end }}. 
```

Multi-Language support
----------------------

Fallback method
```
{{#lang=de}}
Meldung!
{{/lang=de}}
{{^lang=de}}
Fallback Notfication!
{{/lang=de}}
```
___
Explicit
```
{{#lang=de}}
Deutsche Meldung!
{{/lang=de}}
{{#lang=en}}
Fallback Notfication!
{{/lang=en}}
```

Example
-------

```
{{#lang=de}}
<h4 style="margin: 0;display: inline-flex;"><i class="fa fa-wrench" style="margin-right: 1ex;"></i>Am {{ tag_de }}, {{ datum_de }} wird Moodle auf die Version {{ version }} aktualisiert. Im Zeitraum von {{ start }} bis {{ end }} wird Moodle nicht erreichbar sein.</h4>
{{/lang=de}}{{^lang=de}}
<h4 style="margin: 0;display: inline-flex;"><i class="fa fa-wrench" style="margin-right: 1ex;"></i>On {{ day }}, {{ date }} Moodle will be updated to version {{ version }}. Moodle will be unavailable from {{ start }} to {{ end }}.</h4>
{{/lang=de}}
```





