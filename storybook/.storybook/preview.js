// Docs: https://storybook.js.org/docs/react/essentials/viewport
const customViewports = {
  small: {
    name: 'small',
    styles: {
      width: '576px',
      height: '100%',
    },
  },
  medium: {
    name: 'medium',
    styles: {
      width: '768px',
      height: '100%',
    },
  },
  large: {
    name: 'large',
    styles: {
      width: '1024px',
      height: '100%',
    },
  },
  xlarge: {
    name: 'xlarge',
    styles: {
      width: '1200px',
      height: '100%',
    },
  },
  xxlarge: {
    name: 'xxlarge',
    styles: {
      width: '1620px',
      height: '100%',
    },
  },
};

export const parameters = {
  server: {
    url: process.env.STORYBOOK_SERVER_URL || 'http://surf.lndo.site',
  },
  drupalTheme: 'surf_main',
  viewport: { viewports: customViewports },
  // supportedDrupalThemes: {
  //   surf_main: {title: 'Surf Main'},
  // },
};
