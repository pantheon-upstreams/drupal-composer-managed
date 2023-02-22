# Storybook
## Overview

This site is integrated with [Storybook](https://storybook.js.org/), a tool for developing UI components in isolation. Storybook runs outside of Drupal and allows you to develop UI components in isolation from the rest of your application. This makes development fast and easy, and helps you build better UI components. The [CL Server](https://drupal.org/project/cl_server) module provides a Storybook integration that allows you to preview and develop your components in an interactive live-reloading Storybook environment.

"Stories" are individual examples of a component in use. A component may have many stories to show how it renders when different props are passed in. For example, a teaser component may have a story showing how it displays with an Image and one without, as well as examples of different modifiers such as a "Featured" teaser. Storybook offers interactive controls that allow users to change props to see how the component renders in different states.


## Setup
First ensure [CL Server](https://drupal.org/project/cl_server) is installed in your Drupal environment. Watch this [screencast](https://drive.google.com/file/d/1fCP8DELUeraEU-LM3zHZFWCtOsDfDzIz/view?usp=share_link) for a quick overview of how storybook is structured.

```bash
drush en cl_server
drush cr
```

In order to take advantage of live reloading, you'll need to disable Drupal's twig cache. You will also need to open the site to CORS requests so the Storybook server can access the CL Server endpoint in Drupal. This endpoint returns the rendered HTML of a component which Storybook will render in an `<iframe>` along with all global and component specific libraries.

In your `services.local.yml` or `services.development.yml`file, add the following parameters:

```yaml
parameters:
  twig.config:
    debug: true
    auto_reload: true
    cache: false
  cors.config:
    enabled: true
    allowedHeaders: ['*']
    allowedMethods: []
    allowedOrigins: ['*']
    exposedHeaders: false
    maxAge: false
    supportsCredentials: true
```

You will also need to disable the component registry cache in Drupal so changes to component files will live reload. This cache is used to store the component definitions and is used to generate the Storybook stories. To disable the cache, add the following to your `settings.local.php` or `settings.development.php` file:

```php
# Disable caches during development. This allows finding new components without clearing caches.
$settings['cache']['bins']['component_registry'] = 'cache.backend.null';
```

And disable JS and CSS aggregation in `settings.local.php`:

```php
/**
 * Disable CSS and JS aggregation.
 */
$config['system.performance']['css']['preprocess'] = FALSE;
$config['system.performance']['js']['preprocess'] = FALSE;
```

Now you need to grant permissions to the CL Server endpoint for anonymous users. This is required so Storybook can access the endpoint. To do this, visit `/admin/people/permissions` and check `Use the CL Server endpoint` permission to the `Anonymous User` role. *Do not* check this permission in production or on publicly accessible environments as it has security implications. We will need to revisit this if we want the stories to be publicly accessible or available on a public environment.

Now change to the `/storybook` directory in the root of the project and run:

```bash
npm install
```

Storybook is configured to fetch components form 'https://surf.lndo.site'. If you are using a different domain, you will need to set the `STORYBOOK_SERVER_URL` environment variable. To do so, cop `storybook/.env.example` to `storybook/.env` file and update the `STORYBOOK_SERVER_URL` to match your environment.

## Running Storybook
To run Storybook, change to the `/storybook` directory in the root of the project and run:

```bash
npm run storybook
```

The browser should open to `http://localhost:6006` and you should see the Storybook UI. If you don't see the UI, you can manually navigate to `http://localhost:6006` in your browser.

## Deploying Storybook
This instance of Storybook can be deployed to Pantheon alongside the rest of the Drupal site. To deploy Storybook, run the following commands from this `/storybook` directory:

```bash
npm run build-storybook
```

<blockquote>
  <p><strong>Note:</strong> This command will build the static Storybook files in the <code>/web/storybook/</code> directory and include a hardcoded domain reference to https://dev-surf-main.pantheonsite.io/. Because of this, the static build of storybook will only work when deployed to the Pantheon dev environment. We can make this more dynamic once a CI process is implemented. See the `build-storybook` command defined in `package.json`</p>
</blockquote>

<blockquote>
  <p><strong>Note:</strong> You must have CSS and JS aggregation disabled in the Drupal site with CL Server installed for storybook to work. Because of this, Storybook should not be enabled on a production site. There is configuration in `settings.php` to disable aggregation on the dev environment on Pantheon.</p>
</blockquote>

## Defining Stories
See `web/themes/custom/surf_main/README.md` for more information on defining stories for a components.

For examples, take a look at existing stories defined in `web/themes/custom/surf_main/templates/components` and `web/modules/contrib/cl_components/cl_components_examples`.
