README.md for Hearsay Sitemap Taxonomy Module
-------------------------------------

CONTENTS OF THIS FILE
---------------------
* Introduction
* Requirements
* Installation
* Explanation of the Module Files

INTRODUCTION
------------
The module provides the functionality to create a Sitemap taxonomy.

Requirements
------------
NA

Installation
------------
- Enable the module

Explanation of the Module Files
--------------------------------
The Hearsay Sitemap Taxonomy Module contains the following files with respective functionality

- hearsay_sitemap_taxonomy.info.yml
    - Contains module information.

Module contains following files inside /config/install folder.

- taxonomy.vocabulary.hearsay_menu.yml
    - Creation of Sitemap taxonomy

- field.field.taxonomy_term.hearsay_menu.field_content_type_for_page.yml
    - Creation of 'Content type for page' field which refers to the Content type within the site.

- field.field.taxonomy_term.hearsay_menu.field_path_url.yml
    - Creation of 'Path Url' field which refers to the path of content type within Site.

- field.storage.taxonomy_term.field_content_type_for_page.yml
    - Creation of database storage for 'Content type for page' field.

- field.storage.taxonomy_term.field_path_url.yml
    - Creation of database storage for 'Path Url' field.

- core.entity_form_display.taxonomy_term.hearsay_menu.default.yml
    - Refers to the form display of Sitemap taxonomy

- core.entity_view_display.taxonomy_term.hearsay_menu.default.yml
    - Refers to the view display of Sitemap taxonomy