README.md for Hearsay How We Work With You Module
-------------------------------------

CONTENTS OF THIS FILE
---------------------
* Introduction
* Requirements
* Installation
* Explanation of the Module Files

INTRODUCTION
------------
The module provides the functionality to display How We Work With You Section on Home, About us and How we work with you page.

Requirements
------------
NA

Installation
------------
- Enable the module

Explanation of the Module Files
--------------------------------
The Hearsay How We Work With You Module contains the following files with respective functionality

- hearsay_work_with_you.info.yml
  - Contains module information.

- hearsay_work_with_you.module
  - Contains hook_theme function specifying the array to be returned to twig template.

- src/HearsayWorkWithYou.php
  - Contains the php logic for processing the values from profile API and config files and return values to module file.