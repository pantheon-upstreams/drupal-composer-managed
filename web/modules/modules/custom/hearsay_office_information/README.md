README.md for Hearsay Office Information Module
-------------------------------------

CONTENTS OF THIS FILE
---------------------
* Introduction
* Requirements
* Installation
* Explanation of the Module Files

INTRODUCTION
------------
The module provides the functionality to display Office Information Section on Home page.

Requirements
------------
NA

Installation
------------
- Enable the module

Explanation of the Module Files
--------------------------------
The Hearsay Office Information Module contains the following files with respective functionality

- hearsay_office_information.info.yml
  - Contains module information.

- hearsay_office_information.module
  - Contains hook_theme function specifying the array to be returned to twig template.
  
- templates/hearsay-office_information.html.twig
  - Contains the html code for display of block

- src/HearsayOfficeInformation.php
  - Contains the php logic for processing the values from profile API and config files and return values to module file.