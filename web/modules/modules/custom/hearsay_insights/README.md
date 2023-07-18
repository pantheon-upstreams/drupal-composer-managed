README.md for Hearsay Insights Module
-------------------------------------

CONTENTS OF THIS FILE
---------------------
* Introduction
* Requirements
* Installation
* Explanation of the Module Files

INTRODUCTION
------------
The module provides the functionality to display Insights Section on Insights page.

Requirements
------------
NA

Installation
------------
- Enable the module

Explanation of the Module Files
--------------------------------
The Hearsay Insights Module contains the following files with respective functionality

- hearsay_insights.info.yml
  - Contains module information.

- hearsay_insights.module
  - Contains hook_theme function specifying the array to be returned to twig template.

- src/HearsayInsights.php
  - Contains the php logic for processing the values from profile API and config files and return values to module file.