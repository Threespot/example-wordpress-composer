const configMaker = require('./shared/config-maker');
const id = 'backstop_local';
const referenceDomain = 'FIXME:domain';
const testDomain = 'http://FIXME:app-name.lndo.site';

module.exports = configMaker({ id, referenceDomain, testDomain });