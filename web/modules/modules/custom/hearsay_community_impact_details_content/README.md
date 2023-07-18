README.md for Hearsay Community Impact Details Content Type Module
-------------------------------------

# CONTENTS OF THIS FILE

  - Introduction
  - Requirements
  - Installation
  - Explanation of the Module Files

---------------------

## INTRODUCTION
------------

The module provides the functionality to create a Hearsay Community Impact Details content type for Community Impact Details page.



## REQUIREMENTS
------------

NA


## INSTALLATION
------------

- Enable the module


## Explanation of the Module Files
--------------------------------

The Hearsay Community Impact Details Page Content Type Module contains the following files with respective functionality

- hearsay_community_impact_details_content.info.yml.info.yml
  - Contains module information.


Module contains following files inside /config/install folder.

- node.type.community_impact_details.yml
  - Creation of content type

- field.storage.node.file_name.yml
  - Creation of field storage for each file_name
    
- field.field.node.community_impact_details.file_name.yml
  - Creation of field for each file_name

- core.entity_view_display.node.community_impact_details.default.yml
  - defining View display of content type.

- core.entity_form_display.node.community_impact_details.default.yml
  - defining Form display on content type.