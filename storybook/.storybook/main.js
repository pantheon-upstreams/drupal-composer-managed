module.exports = {
  "stories": [
    "../../web/themes/**/*.stories.mdx",
    "../../web/themes/**/*.stories.@(json|yml)",
    // "../../web/modules/**/*.stories.mdx",
    // "../../web/modules/**/*.stories.@(json|yml)",
  ],
  "addons": [
    "@storybook/addon-links",
    {
      name: '@storybook/addon-essentials',
      options: {
        // Docs are disabled for now as their controls are broken
        // and the rendering of multiple stories has performance issues.
        docs: false,
      }
    },
    '@storybook/addon-a11y',
    '@lullabot/storybook-drupal-addon',
  ],
  "framework": "@storybook/server",
  "core": {
    "builder": "@storybook/builder-webpack5"
  },
  // reactOptions: {
  //   fastRefresh: true,
  // },
}
