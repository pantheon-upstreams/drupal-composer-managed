README.md for Hearsay Short Bio Module
-------------------------------------

CONTENTS OF THIS FILE
---------------------
* Introduction
* Requirements
* Installation
* Explanation of the Module Files

INTRODUCTION
------------
The module provides the functionality to display Short Bio Section on Home Page.

Requirements
------------
NA

Installation
------------
- Enable the module

Explanation of the Module Files
--------------------------------
The Hearsay Short Bio Module contains the following files with respective functionality

- hearsay_short_bio.info.yml
  - Contains module information.

- hearsay_short_bio.module
  - Contains hook_theme function specifying the array to be returned to twig template.

- src/HearsayShortBio.php
  - Contains the php logic for processing the values from profile API and config files and return values to module file.