README.md for Hearsay Preview Module
------------------------------------

# CONTENTS OF THIS FILE

  - Introduction
  - Requirements
  - Installation
  - Explanation of the Module Files

---------------------

## INTRODUCTION
---------------

This module is created to Post Create/Delete Preview API Requests
 using rest resources for all the available Base Slugs (Sites) in system.


## REQUIREMENTS
---------------
This module requires below modules for smooth functioning
- rest.
- restui.
- serialization.
- basic_auth.
- jsonapi.
- hearsay_common.

## INSTALLATION
------------

- Enable the module.

## Explanation of the Module Files
----------------------------------

-  DeletePreview.php
    - Used to set expired_page template for expired preview links.

-  ExpiredPreview.php
    - Rest resource(/api/delete_preview) used to post preview deletion requests
     to update expired status for the slug and delete the expired slugs.

-  PostPreview.php
    - Rest resource(/api/preview) used to post new preview requests to save
     API response data in preview configuration.

-  SiteTools.php
    - Includes functions for preview related functionality.
