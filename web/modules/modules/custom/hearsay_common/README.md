README.md for Hearsay Common Module
-------------------------------------

CONTENTS OF THIS FILE
---------------------
* Introduction
* Requirements
* Installation
* Explanation of the Module Files

INTRODUCTION
------------
The module provides common functions for getting the profile data using API, Media Image, Media Elements and current base slug.

Requirements
------------
This module requires below modules for smooth functioning 
- hearsay_config.
- hearsay_client_customization.
- hearsay_automation_process.
- hearsay_preview.

Installation
------------
- Enable the module

Explanation of the Module Files
--------------------------------
-  HearsayCommon.php
    - Includes functions for getting the profile data using API, Media Image, Media Elements and current base slug.

- hearsay_common.info.yml
    - Contains module information.

-  HearsayClientCustomizationInterface.php
    - Includes functions those are mandatory to implement in HearsayClientCustomization class in hearsay_client_customization module.

-  HearsayBaseContactInterface.php
    - Includes functions those are mandatory to implement in HSContactController class in hearsay_client_customization module.

-  HearsayHeaderInterface.php
    - Includes functions those are mandatory to implement in HSHeaderController class in hearsay_client_customization module.

-  HearsayPlatformSettingInterface.php
    - Includes functions those are mandatory to implement in HSPlatformSettingController class in hearsay_client_customization module.
