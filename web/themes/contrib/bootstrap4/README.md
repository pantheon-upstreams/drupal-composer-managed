# Bootstrap 4 theme

## INTRODUCTION

This is a very non-prescriptive vanilla Bootstrap 4 theme 
with simple configuration. It can be used out of the box or 
as a subtheme for creating very flexible web designs with 
minimal changes (just override Bootstrap 4 variables.scss 
and recompile css!)

## FEATURES

* Bootstrap 4 library (4.6.2) included
* Bootstrap 4 style guide (view all Bootstrap 4 components on one page)
* Bootstrap 4 breakpoints
* Bootstrap 4 integration with CKEditor
* Bootstrap 4 configuration within admin user interface
* Interface for creating subtheme
* Can be used as is (subtheme is required for template overrides)
* Drupal 7, 9 and 10 compatible

## SASS compilation:

* SASS compilation is no longer in the theme.

## REQUIREMENTS

### Installation: composer
INSTALLATION

`composer require drupal/bootstrap4`

Head to `Appearance` and install bootstrap4 theme.

## CONFIGURATION

Head to `Appearance` and clicking bootstrap4 `settings`.

### Subtheme

_SUBTHEME folder was removed and is no longer available.

Subtheme can be created via UI interface in theme settings (`Appearance` menu).

## Development and patching

- Install development dependencies by running `npm install`
- To lint SASS files run `npm run lint:sass` (it will fail build if lint fails)
- To lint JS files run `npm run lint:js` (it will fail build if lint fails)
- To compile SASS (for Bootstrap 4.6.2) run `sass scss/style.scss css/style.css` (requires [SASS compiler](https://sass-lang.com/install))
- To compile JS: run `npm run build:js`
- optional: create symlink from bootstrap4 repo folder to a local Drupal installation to simplify development `ln -s /path/to/bootstrap4 /path/to/local-drupal-site/web/themes/contrib`

## FAQ

### FAQ - Menu subnesting

Nesting is considered bad practice in Bootstrap 4. It is bad for UX, mobile 
usage and accessibility.

Hence, there are no examples in the [current documentation](https://getbootstrap.com/docs/4.5/components/dropdowns/#menu-items).

Read more: 

* https://github.com/twbs/bootstrap/issues/27659
* https://github.com/twbs/bootstrap/issues/16387#issuecomment-97153831

Theme developers need to implement their own solution if they are catering 
for multi level menus.

To get started copy `templates/navigation/menu--main.html.twig` to your 
subtheme and modify as follows:

```
{% import _self as menus %}

{#
We call a macro which calls itself to render the full tree.
@see http://twig.sensiolabs.org/doc/tags/macro.html
#}
{{ menus.build_menu(items, attributes, 0) }}

{% macro build_menu(items, attributes, menu_level) %}
  {% import _self as menus %}
  {% if items %}
    {% if menu_level == 0 %}
    <ul{{ attributes.addClass('navbar-nav mr-auto') }}>
    {% else %}
    <ul class="dropdown-menu">
    {% endif %}
    {% for item in items %}
      {{ menus.add_link(item, attributes, menu_level) }}
    {% endfor %}
    </ul>
  {% endif %}
{% endmacro %}

{% macro add_link(item, attributes, menu_level) %}
  {% import _self as menus %}
  {%
    set list_item_classes = [
      'nav-item',
      item.is_expanded ? 'dropdown',
      item.is_expanded and (menu_level > 0) ? 'dropdown-submenu',
    ]
  %}
  {%
    set link_class = [
      'nav-item',
      'nav-link',
      item.in_active_trail ? 'active',
      menu_level == 0 and (item.is_expanded or item.is_collapsed) ? 'dropdown-toggle',
    ]
  %}
  {%
    set toggle_class = [
    ]
  %}
  <li{{ item.attributes.addClass(list_item_classes) }}>
    {% if menu_level == 0 %}
      {{ link(item.title, item.url, { 'class': link_class, 'data-toggle' : 'dropdown', 'title': ('Expand menu' | t) ~ ' ' ~ item.title }) }}
    {% else %}
      {{ link(item.title, item.url, { 'class': link_class }) }}
    {% endif %}
    {% if item.below %}
      {{ menus.build_menu(item.below, attributes, menu_level + 1) }}
    {% endif %}
  </li>
{% endmacro %}
```

## Upgrade to branch 3.0.x

### drush 11

- When running `drush updb`, make sure you are running drush 11 otherwise you 
  might run into errors.
- Run updates via drupal interface if using drush 10 or less.
- If errors already appeared, use either `drush theme:uninstall claro stable` 
  and/or `drush theme:install stable9` depending on what php error you'll get.

### Configuration

If using configuration synchronisation, make sure your core.extension.yml contains

```
theme:
...
stable9: 0
```

instead of

```
theme:
...
stable: 0
classy: 0
```
