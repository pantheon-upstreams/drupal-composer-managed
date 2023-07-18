README.md for Hearsay Additional Information Module
-------------------------------------

CONTENTS OF THIS FILE
---------------------
* Introduction
* Requirements
* Installation
* Explanation of the Module Files

INTRODUCTION
------------
The module provides the functionality to display Additional Information Section on Additional Information page.

Requirements
------------
NA

Installation
------------
- Enable the module

Explanation of the Module Files
--------------------------------
The Hearsay Additional Information Module contains the following files with respective functionality

- hearsay_additional_information.info.yml
  - Contains module information.

- hearsay_additional_information.module
  - Contains hook_theme function specifying the array to be returned to twig template.

- src/HearsayAdditionalInformation.php
  - Contains the php logic for processing the values from profile API and config files and return values to module file.