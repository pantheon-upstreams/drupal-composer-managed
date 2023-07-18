README.md for Hearsay Events Detail Content Type Module
-------------------------------------

# CONTENTS OF THIS FILE

  - Introduction
  - Requirements
  - Installation
  - Explanation of the Module Files

---------------------

## INTRODUCTION
------------

The module provides the functionality to create a Hearsay Events Detail content type for Events Detail page.



## REQUIREMENTS
------------

NA


## INSTALLATION
------------

- Enable the module


## Explanation of the Module Files
--------------------------------

The Hearsay Events Detail Page Content Type Module contains the following files with respective functionality

- hearsay_events_detail_content.info.yml.info.yml
  - Contains module information.


Module contains following files inside /config/install folder.

- node.type.events_detail.yml
  - Creation of content type

- field.storage.node.file_name.yml
  - Creation of field storage for each file_name
    
- field.field.node.events_detail.file_name.yml
  - Creation of field for each file_name

- core.entity_view_display.node.events_detail.default.yml
  - defining View display of content type.

- core.entity_form_display.node.events_detail.default.yml
  - defining Form display on content type.