README.md for Hearsay Events Content Type Module
-------------------------------------

# CONTENTS OF THIS FILE

  - Introduction
  - Requirements
  - Installation
  - Explanation of the Module Files

---------------------

## INTRODUCTION
------------

The module provides the functionality to create a Hearsay Events content type for Events page.



## REQUIREMENTS
------------

NA


## INSTALLATION
------------

- Enable the module


## Explanation of the Module Files
--------------------------------

The Hearsay Events Page Content Type Module contains the following files with respective functionality

- hearsay_events_content.info.yml.info.yml
  - Contains module information.


Module contains following files inside /config/install folder.

- node.type.events.yml
  - Creation of content type

- field.storage.node.file_name.yml
  - Creation of field storage for each file_name
    
- field.field.node.events.file_name.yml
  - Creation of field for each file_name

- core.entity_view_display.node.events.default.yml
  - defining View display of content type.

- core.entity_form_display.node.events.default.yml
  - defining Form display on content type.