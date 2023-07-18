README.md for Hearsay Footer Module
-------------------------------------

CONTENTS OF THIS FILE
---------------------
* Introduction
* Requirements
* Installation
* Explanation of the Module Files

INTRODUCTION
------------
The module provides the functionality to display footer.

Requirements
------------
NA

Installation
------------
- Enable the module

Explanation of the Module Files
--------------------------------
The Hearsay Footer Module contains the following files with respective functionality

- hearsay_footer.info.yml
  - Contains module information.

- hearsay_footer.module
  - Contains hook_theme function specifying the array to be returned to twig template.
  
- templates/hearsay_footer.html.twig
  - Contains the html code for display of block

- src/HearsayFooter.php
  - Contains the php logic for processing the values from profile API and config files and return values to module file.