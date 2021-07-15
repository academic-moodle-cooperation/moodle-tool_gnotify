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






