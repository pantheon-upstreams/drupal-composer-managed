# Sanford Underground Research Facility (SURF Main) - Drupal 9
## Overview

This theme was generated from the contrib theme Prototype utitilizng Drupal's StarterKit. Additional information on generating themes can be found in the [Starterkit documentation](https://www.drupal.org/docs/core-modules-and-themes/core-themes/starterkit-theme).

## Development Workflow

When adding new features to the project you'll need to create a feature branch, commonly this is the Jira ticket number (e.g. SRRM-XXX). You'll commit all your code changes to this feature branch and push the branch to the code repository. Once the ticket is ready for QA, it should be merged into the `master` branch. Which will then be deployed to the development instance on the Pantheon platform.

Assign the Jira ticket to the QA team member, provide the link to the develop environment, and include instructions on what should be tested. Also, please make sure to set up the environment with dummy data to make sure it's working for you prior to getting the QA team involved.

## Theme Development

Powered by some of the latest and greatest tools this package streamlines theming development. To get started navigate into this folder through the cli, if you have NVM installed it should already validate your node version, this project is currently supports v16.17.0.

First, lets start by validating the node version for the project:

```
nvm use
```

If you do not have the required version, please install it:

```
nvm install
```

Next you'll need to install the required npm packages:

```
npm install
```

That's it for installs! You can start developing by running:

```
npm run watch
```

To compile your build files, stop watching and run:

```
npm run build
```
