# Sanford Underground Research Facility (SURF Main) - Drupal 9

- Production URL: https://sanfordlab.org/
- Aten Shortname: SRRM

## Overview

This project provides an updated theme & additional functionality into the SURF main website.

## People & Roles in Project

**Aten**

- Janice Camacho (Project Manager)
- Justin Toupin (Tech Lead)
- Jennifer Dust (Full Stack Developer)
- Alan Sherry (Back End Developer)
- Brent Robbins (Front End Developer)

## Communication

- Slack: #client-surf
- JIRA: https://atendesign.atlassian.net/jira/software/projects/SRRM/boards/425

## Code Repository

- Host: Pantheon
- Repo Name: SURF Main
- Development URL: https://dev-surf-main.pantheonsite.io/
- Repo URL: https://dashboard.pantheon.io/sites/cb68cc29-02f1-42bc-8e9a-1b212c282647#dev/code
- Default Branch: `master`
- Owner: Sanford Underground Research Facility

## Hosting

**Production**

- **Provider:** Pantheon
- **Owner:** Sanford Underground Research Facility

## Development Workflow

When adding new features to the project you'll need to create a feature branch, commonly this is the Jira ticket number (e.g. SRRM-XXX). You'll commit all your code changes to this feature branch and push the branch to the code repository. Once the ticket is ready for QA, it should be merged into the `master` branch. Which will then be deployed to the development instance on the Pantheon platform.

Assign the Jira ticket to the QA team member, provide the link to the develop environment, and include instructions on what should be tested. Also, please make sure to set up the environment with dummy data to make sure it's working for you prior to getting the QA team involved.

1. Hotfix/Non-Release Deployments
   1. Make sure your local environment does not have uncommitted changes
   2. If your local database is very outdated (over a month), import a new one from production
   3. On the master branch `git checkout master`
   4. VERY Important: Import configuration changes `drush cim sync -y` Failing to do so will result in overriding configuration
   5. Create a feature branch based on the Jira ticket name `git checkout -b SRRM-###`
   6. Make necessary code changes. If changing configuration remember to export configuration `drush cex sync -y`
      1. Review changes you've made with `git diff`
   7. Commit changes to your feature branch `git add .` and `git commit -m "SRRM-###: note about the change" and `git push`
   8. Merge into staging branch for testing on Acquia `git checkout staging` and `git merge SRRM-###`
   9. After your changes are approved on staging, merge your feature branch into the master branch and deploy
      1. `git checkout master` and `git merge SRRM-###` and `git push`
2. Using a Release/Sprint Branch - Useful if working on a bunch of features
   1. Create the release branch
      1. Do Once - `git checkout master` && `git checkout -b release-1`
      2. Push the release branch for everyone to work from
   2. Participating in the release
      1. Make sure your local environment does not have uncommitted changes
      2. If your local database is very outdated (over a month), import a new one from production
      3. On the master branch `git checkout release-1`
      4. VERY important: Import configuration changes `drush cim sync -y` Failing to do so will result in overriding configuration
      5. Create a feature branch of the release branch based on the Jira ticket name `git checkout -b SRRM-###`
      6. Make necessary code changes. If changing configuration remember to export configuration `drush cex sync -y`
         1. Review changes you've made with `git diff`
      7. Commit changes to your feature branch `git add .` and `git commit -m "SRRM-###: note about the change" and `git push`
      8. Merge into release branch for testing on Acquia `git checkout release-1` and `git ◊merge SRRM-###`
         1. On Acquia use one environment dedicated to the release
      9. After your changes are approved on staging, merge your feature branch into the master branch and deploy
         1. `git checkout master` and `git merge SRRM-###` and `git push`
   3. Deploying the release
      1. You may want to merge master into your release branch to catch any divergence since the branch was created
      2. Merge your release branch into the staging branch to test the release
      3. Once tested, merge the release branch into master
   4. Caveats: If work has been started on the release branch but not finished, it will need to be manually removed

## Automated Testing

- This project does not currently have automated testing configured.

## Theme Information

- Base Theme: Prototype
- Custom Theme: `surf_main`
- Location to READ.ME: `web/themes/custom/surf_main/README.md`

## Deployment

More to come...

## Local Development

The Drupal project was setup to support Lando out of the box. Developers can quickly get started setting up your local environment by following the instructions below. Please make sure you've installed [Lando](https://docs.lando.dev/basics/installation.html).

First, lets start by installing the project composer packages:

```
composer install
```

Now, you'll need to start up the Lando instance:

```
lando start
```

Now, you'll need to import the development database to the local environment:

```
lando db-import localdatabasename.sql
```

## Updating Drupal Core and Contrib modules with Composer

Following the development workflow, updates should be done exclusively with Composer (see https://www.drupal.org/docs/develop/using-composer/manage-dependencies)

1. Updating Drupal core - See https://www.drupal.org/docs/updating-drupal/updating-drupal-core-via-composer
   1. `composer update drupal/core --with-dependencies`
   2. `drush updatedb`
   3. `drush cache:rebuild`
2. Updating Drupal modules
   1. `composer update drupal/modulename --with-dependencies`
   2. `drush updatedb`
   3. `drush cache:rebuild`
3. Removing Drupal modules
   1. Note: Be careful doing this. A module MUST be uninstalled on production before you choose to remove from the code base via Composer (this would mean two deploys)
   2. Uninstall the module, export configuration, push code, and deploy to production
   3. After the module is uninstalled on production, you can remove it from the code base using Composer by `composer remove drupal/modulename`
4. Patches
   1. Patches should be done exclusively with Composer as well. It allows all developers to be aware of what modules are getting patched and if any of them fail to apply after an update
