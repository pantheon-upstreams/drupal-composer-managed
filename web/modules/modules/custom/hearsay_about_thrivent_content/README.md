README.md for Hearsay About Thrivent Content Type Module
-------------------------------------

# CONTENTS OF THIS FILE

  - Introduction
  - Requirements
  - Installation
  - Explanation of the Module Files

---------------------

## INTRODUCTION
------------

The module provides the functionality to create a Hearsay About Thrivent content type for About Thrivent page.



## REQUIREMENTS
------------

NA


## INSTALLATION
------------

- Enable the module


## Explanation of the Module Files
--------------------------------

The Hearsay About Thrivent Page Content Type Module contains the following files with respective functionality

- hearsay_about_thrivent_content.info.yml.info.yml
  - Contains module information.


Module contains following files inside /config/install folder.

- node.type.about_thrivent.yml
  - Creation of content type

- field.storage.node.file_name.yml
  - Creation of field storage for each file_name
    
- field.field.node.about_thrivent.file_name.yml
  - Creation of field for each file_name

- core.entity_view_display.node.about_thrivent.default.yml
  - defining View display of content type.

- core.entity_form_display.node.about_thrivent.default.yml
  - defining Form display on content type.