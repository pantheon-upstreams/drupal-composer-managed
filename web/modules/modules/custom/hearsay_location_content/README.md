README.md for Hearsay Location Content Type Module
-------------------------------------

# CONTENTS OF THIS FILE

  - Introduction
  - Requirements
  - Installation
  - Explanation of the Module Files

---------------------

## INTRODUCTION
------------

The module provides the functionality to create a Hearsay Location content type for Location page.



## REQUIREMENTS
------------

NA


## INSTALLATION
------------

- Enable the module


## Explanation of the Module Files
--------------------------------

The Hearsay Location Page Content Type Module contains the following files with respective functionality

- hearsay_location_content.info.yml.info.yml
  - Contains module information.


Module contains following files inside /config/install folder.

- node.type.location.yml
  - Creation of content type

- field.storage.node.file_name.yml
  - Creation of field storage for each file_name
    
- field.field.node.location.file_name.yml
  - Creation of field for each file_name

- core.entity_view_display.node.location.default.yml
  - defining View display of content type.

- core.entity_form_display.node.location.default.yml
  - defining Form display on content type.