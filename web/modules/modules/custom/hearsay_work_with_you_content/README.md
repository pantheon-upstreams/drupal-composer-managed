README.md for Hearsay Work With You Content Type Module
-------------------------------------

# CONTENTS OF THIS FILE

  - Introduction
  - Requirements
  - Installation
  - Explanation of the Module Files

---------------------

## INTRODUCTION
------------

The module provides the functionality to create a Hearsay Work With You content type for Work With You page.



## REQUIREMENTS
------------

NA


## INSTALLATION
------------

- Enable the module


## Explanation of the Module Files
--------------------------------

The Hearsay Work With You Page Content Type Module contains the following files with respective functionality

- hearsay_work_with_you_content.info.yml.info.yml
  - Contains module information.


Module contains following files inside /config/install folder.

- node.type.work_with_you.yml
  - Creation of content type

- field.storage.node.file_name.yml
  - Creation of field storage for each file_name
    
- field.field.node.work_with_you.file_name.yml
  - Creation of field for each file_name

- core.entity_view_display.node.work_with_you.default.yml
  - defining View display of content type.

- core.entity_form_display.node.work_with_you.default.yml
  - defining Form display on content type.