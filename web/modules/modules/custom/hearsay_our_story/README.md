README.md for Hearsay Our Story Module
-------------------------------------

CONTENTS OF THIS FILE
---------------------
* Introduction
* Requirements
* Installation
* Explanation of the Module Files

INTRODUCTION
------------
The module provides the functionality to display Our Story Section on Our Story page.

Requirements
------------
NA

Installation
------------
- Enable the module

Explanation of the Module Files
--------------------------------
The Hearsay Our Story Module contains the following files with respective functionality

- hearsay_our_story.info.yml
  - Contains module information.

- hearsay_our_story.module
  - Contains hook_theme function specifying the array to be returned to twig template.

- src/HearsayOurStory.php
  - Contains the php logic for processing the values from profile API and config files and return values to module file.