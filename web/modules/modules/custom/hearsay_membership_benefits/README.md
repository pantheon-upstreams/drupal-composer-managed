README.md for Hearsay Membership Benefits Module
-------------------------------------

CONTENTS OF THIS FILE
---------------------
* Introduction
* Requirements
* Installation
* Explanation of the Module Files

INTRODUCTION
------------
The module provides the functionality to display Membership Benefits Section.

Requirements
------------
NA

Installation
------------
- Enable the module

Explanation of the Module Files
--------------------------------
The Hearsay Membership Benefits Module contains the following files with respective functionality

- hearsay_membership_benefits.info.yml
  - Contains module information.

- hearsay_membership_benefits.module
  - Contains hook_theme function specifying the array to be returned to twig template.

- src/HearsayMembershipBenefits.php
  - Contains the php logic for processing the values from profile API and config files and return values to module file.