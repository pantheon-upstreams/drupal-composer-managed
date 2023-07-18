README.md for Hearsay Trailing Slash Module
-------------------------------------

# CONTENTS OF THIS FILE

  - Introduction
  - Requirements
  - Installation
  - Explanation of the Module Files

---------------------

## INTRODUCTION
------------

The module provides the functionality to add '/' at the end of URL in case it is not present. This module also adds '/' at the end of sitemap.xml.



## REQUIREMENTS
------------

NA


## INSTALLATION
------------

- Enable the module


## Explanation of the Module Files
--------------------------------

The Hearsay Solutions Page Content Type Module contains the following files with respective functionality

- hearsay_trailing_slash.info.yml.info.yml
  - Contains module information.

- hearsay_trailing_slash.services.yml
  - Defining the service for module

- src\PathProcessor\TrailingSlashPathProcessor.php
  - Creation of class and functionality for adding trailing slash using Outbound Path Processor Interface.