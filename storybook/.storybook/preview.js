const customViewports = {
  small: {
    name: 'small',
    styles: {
      width: '576px',
      height: '963px',
    },
  },
  medium: {
    name: 'medium',
    styles: {
      width: '768px',
      height: '963px',
    },
  },
  large: {
    name: 'large',
    styles: {
      width: '1024px',
      height: '963px',
    },
  },
  xlarge: {
    name: 'xlarge',
    styles: {
      width: '1200px',
      height: '963px',
    },
  },
  xxlarge: {
    name: 'xxlarge',
    styles: {
      width: '1620px',
      height: '963px',
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
