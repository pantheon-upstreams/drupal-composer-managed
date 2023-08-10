README.md for Hearsay Automation Process Module
-------------------------------------

# CONTENTS OF THIS FILE
---------------------
* Introduction
* Requirements
* Installation
* Explanation of the Module Files

## INTRODUCTION
------------
The module provides the functionality to create Base Slugs and Nodes from API response by processing the data into the queue.
This module provides a cron which imports the sites into the queue.

## Requirements
------------
This module requires below module for smooth functioning 
- hearsay_config.
- hearsay_common.
- hearsay_preview.

## Installation
------------
- Enable the module
- Run the cron named 'Hearsay Micro-site Automation Process'.
- Process the queue named 'HS Import Processing Queue'.

## Explanation of the Module Files
--------------------------------
The Hearsay Automation Process Module contains the following files with respective functionality

- hearsay_automation_process.info.yml
  - Contains module information.

- hearsay_automation_process.module
  - Contains hook_cron function which created the cron for processing the API data.
  
- hearsay_automation_process.services.yml
  - Defines the utility service 'HSUtilityService'.

- HSUtilityService.php
  - Contains the utility service functions for processing the API data and creating Base Slugs and Nodes.

- /QueueWorker/HSQueueWorker.php
  - Contains the Queue Worker function for creating the Queue and processing the Queue Items.
