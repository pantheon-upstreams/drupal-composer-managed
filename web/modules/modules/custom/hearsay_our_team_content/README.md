README.md for Hearsay Our Team Content Type Module
-------------------------------------

# CONTENTS OF THIS FILE

  - Introduction
  - Requirements
  - Installation
  - Explanation of the Module Files

---------------------

## INTRODUCTION
------------

The module provides the functionality to create a Hearsay Our Team content type for Our Team page.



## REQUIREMENTS
------------

NA


## INSTALLATION
------------

- Enable the module


## Explanation of the Module Files
--------------------------------

The Hearsay Our Team Page Content Type Module contains the following files with respective functionality

- hearsay_our_team_content.info.yml.info.yml
  - Contains module information.


Module contains following files inside /config/install folder.

- node.type.our_team.yml
  - Creation of content type

- field.storage.node.file_name.yml
  - Creation of field storage for each file_name
    
- field.field.node.our_team.file_name.yml
  - Creation of field for each file_name

- core.entity_view_display.node.our_team.default.yml
  - defining View display of content type.

- core.entity_form_display.node.our_team.default.yml
  - defining Form display on content type.