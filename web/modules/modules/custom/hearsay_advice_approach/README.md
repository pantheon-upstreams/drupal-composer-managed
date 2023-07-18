README.md for Hearsay Our advice approach Module
-------------------------------------

CONTENTS OF THIS FILE
---------------------
* Introduction
* Requirements
* Installation
* Explanation of the Module Files

INTRODUCTION
------------
The module provides the functionality to display Our advice approach Section on Our advice approach page.

Requirements
------------
NA

Installation
------------
- Enable the module

Explanation of the Module Files
--------------------------------
The Hearsay Our advice approach Module contains the following files with respective functionality

- hearsay_advice_approach.info.yml
  - Contains module information.

- hearsay_advice_approach.module
  - Contains hook_theme function specifying the array to be returned to twig template.

- src/HearsayAdviceApproach.php
  - Contains the php logic for processing the values from profile API and config files and return values to module file.