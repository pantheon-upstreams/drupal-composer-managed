README.md for Hearsay Strong and Stable Module
-------------------------------------

CONTENTS OF THIS FILE
---------------------
* Introduction
* Requirements
* Installation
* Explanation of the Module Files

INTRODUCTION
------------
The module provides the functionality to display Strong and Stable Section on Strong and Stable page.

Requirements
------------
NA

Installation
------------
- Enable the module

Explanation of the Module Files
--------------------------------
The Hearsay Strong and Stable Module contains the following files with respective functionality

- hearsay_strong_stable.info.yml
  - Contains module information.

- hearsay_strong_stable.module
  - Contains hook_theme function specifying the array to be returned to twig template.

- src/HearsayStrongStable.php
  - Contains the php logic for processing the values from profile API and config files and return values to module file.