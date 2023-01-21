# Sanford Underground Research Facility (SURF Main) - Drupal 9

- Main URL: https://dev-surf-main.pantheonsite.io/
- Aten Shortname: SRRM

## Overview

This theme was generated from the contrib theme Prototype utitilizng Drupal's StarterKit. Additional information on generating themes can be found in the [Starterkit documentation](https://www.drupal.org/docs/core-modules-and-themes/core-themes/starterkit-theme).

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
- Repo Path: https://dashboard.pantheon.io/sites/cb68cc29-02f1-42bc-8e9a-1b212c282647#dev/code
- Owner: Sanford Underground Research Facility

## Hosting

**Production**

- **Provider:** Pantheon
- **Owner:** Sanford Underground Research Facility

## Developer Workflow

When adding new features to the project you'll need to create a feature branch, commonly this is the Jira ticket number (e.g. SRRM-XXX). You'll commit all your code changes to this feature branch and push the branch to the code repository. Once the ticket is ready for QA, it should be merged into the `master` branch. Which will then be deployed to the development instance on the Pantheon platform.

Assign the Jira ticket to the QA team member, provide the link to the develop environment, and include instructions on what should be tested. Also, please make sure to set up the environment with dummy data to make sure it's working for you prior to getting the QA team involved.

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
