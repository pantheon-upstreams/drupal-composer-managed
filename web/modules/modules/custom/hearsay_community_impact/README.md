README.md for Hearsay Community Impact Module
-------------------------------------

CONTENTS OF THIS FILE
---------------------
* Introduction
* Requirements
* Installation
* Explanation of the Module Files

INTRODUCTION
------------
The module provides the functionality to display Community Impact Section on Community Impact page.

Requirements
------------
NA

Installation
------------
- Enable the module

Explanation of the Module Files
--------------------------------
The Hearsay Community Impact Module contains the following files with respective functionality

- hearsay_community_impact.info.yml
  - Contains module information.

- hearsay_community_impact.module
  - Contains hook_theme function specifying the array to be returned to twig template.

- src/HearsayCommunityImpact.php
  - Contains the php logic for processing the values from profile API and config files and return values to module file.