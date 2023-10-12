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

## Components
This site has the [Component Library (CL) Components](https://drupal.org/project/cl_components) module installed which facilitates the development of self-contained composable components. These components are stored in individual folders inside the `templates/components` folder. Watch this [screencast](https://drive.google.com/file/d/1-h6eX8g6bg7Dm_2GmswLUPIV9qcKRqP3/view?usp=share_link) for an overview of how components are defined and rendered.

<blockquote>
<p><strong>Note:</strong>
At this time, this theme contains two concepts of components. The legacy components are stored in `libraries/components` and the new CL Components are stored in `templates/components`. The legacy components are being phased out in favor of the new components.
</blockquote>

---

### Defining Components
A minimum component consists of a `my-component.twig` file and a `my-component.component.yml` file. The `.twig` file contains the markup for the component. The `.component.yml` file defines the component's name, description, and any available properties. You can think of properties as the component's API, similar to React's component props. They are the inputs that the component accepts. The component's markup is then generated based on the values of the properties.

---

### CSS and JS Assets
In addition to the `.twig` and `.component.yml` files, you can also add `.scss` and `.js` files to the component's `/src/` folder. When the theme's `watch` or `build` scripts run, these files will be compiled to the component's `/css` and `/js` folders respectively. CL Server will automatically attach these compiled assets to page when a component is rendered. No more manually defining libraries for every component you want to create!

If your component depends on another library, you can declare that additional dependency in the `.component.yml` file.

For example:
```yaml
libraryDependencies:
  - core/jquery.once
```
---

### CL Generator
This site includes the [CL Generator](https://drupal.org/project/cl_generator) module which provides a command line tool for generating components. You can install it via Composer:

To generate a component, run the following drush command:
```bash
drush generate cl-component
```
Then follow the prompts.

---
### Rendering Components
There are two ways to render a CL Component. You can either render it directly in your template file, or add it to an existing render array.

#### Twig

Once defined, components will be globally available for use in other templates via Twig's standard `include`, `embed` and `extend` tags. You do not have to worry about twig namespaces which negates the need for the `@` prefix, the [Components](https://drupal.org/project/components) module or tedious `../../../component..` paths.

For example, to render the `my-component` component in a template, you would use the following syntax:
```twig
{% include 'my-component' with {
  title: 'My Component Title',
  content: 'My Component Content'
} %}
```
or use Twigs `include` function syntax: _(Note the double `{{ }}` instead of `{% %}`)_
```twig
{{ include('my-component', {
  title: 'My Component Title',
  content: 'My Component Content'
}) }}
```
or use Twig's `embed` tag to take advantage of the `block` tag, effectively creating component slots:
```twig
{% embed 'my-component' with {
  title: 'My Component Title',
  content: 'My Component Content'
} %}
  {% block content %}
    <div class="necessary-divitis">{{ content }}</div>
    {{ include('my-subcomponent', {text: "I'm a nested subcomponent!"|t}) }}
  {% endblock %}
{% endembed %}
```

#### Render Array

You can also add a component to an existing render array. This is useful if you want to add a component to a view or a block.

For example, to add the `my-component` component to an existing build array using the `cl_component` render element, you would use the following syntax:
```php
$build['my_component'] = [
  '#type' => 'cl_component',
  '#component' => 'my-component',
  '#context' => [
    'title': 'My Component Title',
    'content': 'My Component Content'
  ],
];
```
---
## Storybook Integration
This theme is integrated with [Storybook](https://storybook.js.org/), a tool for developing UI components in isolation. Storybook runs outside of Drupal and allows you to develop UI components in isolation from the rest of your application. This makes development fast and easy, and helps you build better UI components. The [CL Server](https://drupal.org/project/cl_server) module provides a Storybook integration that allows you to preview and develop your components in an interactive live-reloading Storybook environment.

"Stories" are individual examples of a component in use. A component may have many stories to show how it renders when different props are passed in. For example, a teaser component may have a story showing how it displays with an Image and one without, as well as examples of different modifiers such as a "Featured" teaser. Storybook offers interactive controls that allow users to change props to see how the component renders in different states.


### Setup
See `storybook/README.md` for instructions on how to setup Storybook.
### Defining Stories
Each component can include its own `my-component.stories.yml` file.

CL Server, which is responsible for generating the final story consumed by Storybook, will automatically add prop definitions to the story based on the component's `my-component.component.yml` file. These end up as simple `string` text inputs in the Storybook Controls UI. You can further refine those by adding `argTypes` to your story definitions to, for example, change a `color` prop to a select input with a list of available options.

```yaml
# You can organize stories into groups by using the '/' character in titles.
title: Surf Main/My Component
# These are the default args (a.k.a. props) that will be passed to each story.
args:
  heading: Join us at The Conference
  link_text: Register
  link_url: https://www.example.org
  background_style: color
  body: |
    <p><strong>Example paragraph.</strong> Interdum risus tortor turpis gravida sed. Risus sit et egestas tellus ac sed. Purus ut eu fermentum non. Arcu lectus sed in quisque vitae posuere. Adipiscing nullam mauris iaculis leo turpis leo, congue.</p>
# These allow you to dictate how the args are rendered in the controls UI.
argTypes:
  link_target:
    description: The link target attribute
    control: select
    options:
      None: ''
      Blank: '_blank'
  background_style:
    control: select
    description: The background style
    options:
      None: ''
      Color: color
  color:
    control: radio
    description: The callout heading
    options:
      None: ''
      Cloud: cloud
      Forest: forest
      Kelly: kelly
      White: white
# These are the stories themselves. Each story is a unique combination of args that will override the defaults set above.
stories:
  - name: Cloud
    args:
      color: cloud
  - name: Forest
    args:
      color: forest
  - name: Kelly
    args:
      color: kelly
  - name: w/ Image
    args:
      image: '<img src="https://placekitten.com/660/410" alt="Catface" />'
      color: cloud
```

Each of these stories are simply a collection of args (a.k.a. props) passed to a component. Users can then interactively change the values of these args in the Storybook UI to see how the component renders in different states. This will allow you to test different combinations of options and test the boundaries of your component, such as how it may render with a super long title.

For more examples, take a look at existing stories defined in `web/themes/custom/surf_main/templates/components` and `web/modules/contrib/cl_components/cl_components_examples`.
