# Global Notifications (gnotify)

This file is part of the tool_gnotify plugin for Moodle - [http://moodle.org/](http://moodle.org/)

*Author:*   Gregor Eichelberger, Thomas Wedekind, 

*Copyright:* [Academic Moodle Cooperation](http://www.academic-moodle-cooperation.org)

*License:*   [GNU GPL v3 or later](http://www.gnu.org/copyleft/gpl.html)

## Description

Global Notifications (also known as **gnotify**) provides a flexible notification system for Moodle
that allows administrators to display global messages using templates, variables, and
multi-language content. It is especially useful for maintenance announcements, system updates, and
other site-wide information.

The plugin supports:

* Variable-based templates using a simple placeholder syntax
* Multi-language notifications with automatic fallback handling
* HTML content for rich, styled messages

## Template support

Template variables must match the following regular expression:

```
/{{\s*([a-zA-Z0-9_]+?)\s*}}/
```

Example:

```
Moodle maintenance will start on {{ main_date }} at {{ start }} and end at {{ end }}.
```

## Multi-Language support

### Fallback method

If the current language does not match, a fallback notification is displayed automatically:

```
{{#lang=de}}
Meldung!
{{/lang=de}}
{{^lang=de}}
Fallback Notfication!
{{/lang=de}}
```

### Explicit language definitions

You can also define notifications explicitly per language:

```
{{#lang=de}}
Deutsche Meldung!
{{/lang=de}}
{{#lang=en}}
Fallback Notfication!
{{/lang=en}}
```

## Example

A complete example combining variables, HTML, and language handling:

```
{{#lang=de}}
<h4 style="margin: 0;display: inline-flex;"><i class="fa fa-wrench" style="margin-right: 1ex;"></i>Am {{ tag_de }}, {{ datum_de }} wird Moodle auf die Version {{ version }} aktualisiert. Im Zeitraum von {{ start }} bis {{ end }} wird Moodle nicht erreichbar sein.</h4>
{{/lang=de}}{{^lang=de}}
<h4 style="margin: 0;display: inline-flex;"><i class="fa fa-wrench" style="margin-right: 1ex;"></i>On {{ day }}, {{ date }} Moodle will be updated to version {{ version }}. Moodle will be unavailable from {{ start }} to {{ end }}.</h4>
{{/lang=de}}
```

## Usage

The admin tool **Global Notification** serves as a powerful communication tool for administrators of the Moodle platform.

With this tool, administrators can:

Communicate messages (e.g. maintenance work, outages, or planned updates) to all users globally or target specific groups of users

Create and manage multiple notification templates

Define and reuse custom template tags (variables)

Control on which pages notifications are displayed

Restrict visibility based on user roles

Apply additional restrictions using user profile fields

This flexibility allows administrators to deliver precise, relevant information to the right audience at the right time, without modifying code or redeploying the plugin.

## Requirements

No additional requirements.

## Installation

* Copy the code directly to the 'admin/tool/gnotify' directory.

* Log into Moodle as administrator.

* Open the administration area ([http://your-moodle-site/admin](http://your-moodle-site/admin)) to start the installation
  automatically.

## Privacy API

The plugin fully implements the Moodle Privacy API.

## Documentation

You can find a documentation for the plugin on the [AMC website](https://academic-moodle-cooperation.org/tool_gnotify/).

## Bug Reports / Support

We try our best to deliver bug-free plugins, but we can not test the plugin for every platform, database, PHP and Moodle version. If you find any bug please report it on
[GitHub](https://github.com/academic-moodle-cooperation/moodle-tool_gnotify/issues). Please provide a detailed bug description, including the plugin and Moodle version and, if applicable, a screenshot.
You may also file a request for enhancement on GitHub. If we consider the request generally useful and if it can be implemented with reasonable effort we might implement it in a future version.
You may also post general questions on the plugin on GitHub, but note that we do not have the resources to provide detailed support.

## License

This plugin is free software: you can redistribute it and/or modify it under the terms of the GNU
General Public License as published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.

The plugin is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
General Public License for more details.

You should have received a copy of the GNU General Public License with Moodle. If not, see
[http://www.gnu.org/licenses/](http://www.gnu.org/licenses/).

Good luck and have fun!
