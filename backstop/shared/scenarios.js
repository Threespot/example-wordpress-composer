const baseScenario = require('./base-scenario')

// URLs to test
const links = [
  {
    label: 'Hompage',
    path: '/',
    // hideSelectors: ['.cookie-banner']
  },
  {
    label: 'Page Test',
    path: '/page-test'
  },
  // Project Specific Pages
  // {
  //   label: 'Template Projects Listing',
  //   path: '/projects'
  // }

]

module.exports = (referenceDomain, testDomain) =>
  links.map(routeConfig => {
    const config = Object.assign({},
      baseScenario, 
      {
        url: `${testDomain}${routeConfig.path}`,
        referenceUrl: `${referenceDomain}${routeConfig.path}`,
      },
      routeConfig
    )
    return config;
  });
