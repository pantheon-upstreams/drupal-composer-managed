README.md for Hearsay Strong and Stable Content Type Module
-------------------------------------

# CONTENTS OF THIS FILE

  - Introduction
  - Requirements
  - Installation
  - Explanation of the Module Files

---------------------

## INTRODUCTION
------------

The module provides the functionality to create a Hearsay Strong and Stable content type for Strong and Stable page.



## REQUIREMENTS
------------

NA


## INSTALLATION
------------

- Enable the module


## Explanation of the Module Files
--------------------------------

The Hearsay Strong and Stable Page Content Type Module contains the following files with respective functionality

- hearsay_strong_and_stable_content.info.yml.info.yml
  - Contains module information.


Module contains following files inside /config/install folder.

- node.type.strong_and_stable.yml
  - Creation of content type

- field.storage.node.file_name.yml
  - Creation of field storage for each file_name
    
- field.field.node.strong_and_stable.file_name.yml
  - Creation of field for each file_name

- core.entity_view_display.node.strong_and_stable.default.yml
  - defining View display of content type.

- core.entity_form_display.node.strong_and_stable.default.yml
  - defining Form display on content type.