README.md for Hearsay News and Insights Detail Content Type Module
-------------------------------------

# CONTENTS OF THIS FILE

  - Introduction
  - Requirements
  - Installation
  - Explanation of the Module Files

---------------------

## INTRODUCTION
------------

The module provides the functionality to create a Hearsay News and Insights Detail content type for News and Insights Detail page.



## REQUIREMENTS
------------

NA


## INSTALLATION
------------

- Enable the module


## Explanation of the Module Files
--------------------------------

The Hearsay News and Insights Detail Page Content Type Module contains the following files with respective functionality

- hearsay_news_and_insights_detail_content.info.yml.info.yml
  - Contains module information.


Module contains following files inside /config/install folder.

- node.type.news_and_insights_detail.yml
  - Creation of content type

- field.storage.node.file_name.yml
  - Creation of field storage for each file_name
    
- field.field.node.news_and_insights_detail.file_name.yml
  - Creation of field for each file_name

- core.entity_view_display.node.news_and_insights_detail.default.yml
  - defining View display of content type.

- core.entity_form_display.node.news_and_insights_detail.default.yml
  - defining Form display on content type.