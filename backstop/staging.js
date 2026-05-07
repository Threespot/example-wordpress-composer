const configMaker = require('./shared/config-maker')
const id = 'backstop_staging'
const referenceDomain = 'FIXME:domain';
const testDomain = 'https://test-FIXME:app-name.pantheonsite.io'
module.exports = configMaker({ id, referenceDomain, testDomain })