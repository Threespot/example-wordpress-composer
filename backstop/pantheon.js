const configMaker = require('./shared/config-maker');
const id = 'pantheon';
// Stash dev URL, removing any trailing slash
const referenceDomain = process.env.DEV_SITE_URL.replace(/\/$/, "");
// Stash multidev URL, removing any trailing slash
const testDomain = process.env.MULTIDEV_SITE_URL.replace(/\/$/, "");

module.exports = configMaker({ id, referenceDomain, testDomain });