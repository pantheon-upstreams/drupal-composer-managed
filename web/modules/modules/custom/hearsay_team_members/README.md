README.md for Hearsay Team Members Module
-------------------------------------

CONTENTS OF THIS FILE
---------------------
* Introduction
* Requirements
* Installation
* Explanation of the Module Files

INTRODUCTION
------------
The module provides the functionality to display Team Members Section on Home, About Us and Solutions page.

Requirements
------------
NA

Installation
------------
- Enable the module

Explanation of the Module Files
--------------------------------
The Hearsay Team Members Module contains the following files with respective functionality

- hearsay_team_members.info.yml
  - Contains module information.

- hearsay_team_members.module
  - Contains hook_theme function specifying the array to be returned to twig template.
  
- templates/hearsay-team-members.html.twig
  - Contains the html code for display of block

- src/HearsayTeamMembers.php
  - Contains the php logic for processing the values from profile API and config files and return values to module file.