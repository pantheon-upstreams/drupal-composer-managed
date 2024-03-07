# Component - Menu

- **Atomic Classification:** Molecule
- **Status:** Experimental

## Examples

```
{% embed 'surf_main:menu' with {
    title_prefix,
    title_suffix,
    menu_attributes: create_attribute({'class': 'm-menu--footer'}).merge(attributes),
    menu_title: 'Footer menu'|t,
    menu_content: content,
} only %}
    {% block menu_prefix %}
        {{ title_prefix }}
        {{ title_suffix }}
    {% endblock %}
{% endembed %}
```
