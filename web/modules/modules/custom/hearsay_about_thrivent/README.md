README.md for Hearsay About Thrivent Module
-------------------------------------

CONTENTS OF THIS FILE
---------------------
* Introduction
* Requirements
* Installation
* Explanation of the Module Files

INTRODUCTION
------------
The module provides the functionality to display About Thrivent Section on About Thrivent page.

Requirements
------------
NA

Installation
------------
- Enable the module

Explanation of the Module Files
--------------------------------
The Hearsay About Thrivent Module contains the following files with respective functionality

- hearsay_about_thrivent.info.yml
  - Contains module information.

- hearsay_about_thrivent.module
  - Contains hook_theme function specifying the array to be returned to twig template.

- src/HearsayAboutThrivent.php
  - Contains the php logic for processing the values from profile API and config files and return values to module file.