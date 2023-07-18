README.md for Hearsay Base Slug Taxonomy Module
-------------------------------------

CONTENTS OF THIS FILE
---------------------
* Introduction
* Requirements
* Installation
* Explanation of the Module Files

INTRODUCTION
------------
The module provides the functionality to create a Base Slug taxonomy.

Requirements
------------
NA

Installation
------------
- Enable the module

Explanation of the Module Files
--------------------------------
The Hearsay Base Slug Taxonomy Module contains the following files with respective functionality

- hearsay_base_slug_taxonomy.info.yml
    - Contains module information.

Module contains following files inside /config/install folder.

- taxonomy.vocabulary.base_slugs.yml
    - Creation of Base Slug taxonomy

- field.field.taxonomy_term.base_slugs.field_workspace_id.yml
    - Creation of Workspace ID field which refers to the Workspace ID of Site.

- field.field.taxonomy_term.base_slugs.field_theme_id.yml
    - Creation of Theme ID field which refers to the Theme ID of Site.

- field.field.taxonomy_term.base_slugs.field_hearsay_site_name.yml
    - Creation of Site Name field which refers to the Site Name of Site.

- field.field.taxonomy_term.base_slugs.field_baseslug_default_language.yml
    - Creation of Baseslug Default Language field which refers to the language of Site.

- field.field.taxonomy_term.base_slugs.field_account_id.yml
    - Creation of Account ID field which refers to the Account ID of Site.

- field.storage.taxonomy_term.field_workspace_id.yml
    - Creation of database storage for Workspace ID field.

- field.storage.taxonomy_term.field_theme_id.yml
    - Creation of database storage for Theme ID field.

- field.storage.taxonomy_term.field_hearsay_site_name.yml
    - Creation of database storage for Site Name field.

- field.storage.taxonomy_term.field_baseslug_default_language.yml
    - Creation of database storage for Baseslug Default Language field.

- field.storage.taxonomy_term.field_account_id.yml
    - Creation of Account ID field which refers to the Account ID of Site.

- core.entity_form_display.taxonomy_term.base_slugs.default.yml
    - Refers to the form display of Base Slug taxonomy

- core.entity_view_display.taxonomy_term.base_slugs.default.yml
    - Refers to the view display of Base Slug taxonomy