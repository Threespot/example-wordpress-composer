module.exports = {
  onBeforeScript: "puppet/onBefore.js",
  onReadyScript: "puppet/onReady.js",
  report: ['browser', 'CI'],
  engine: 'puppeteer',
  engineOptions: {
    args: ['--no-sandbox']
  },
  engineFlags: [],
  asyncCaptureLimit: 5,
  asyncCompareLimit: 50,
  debug: false,
  debugWindow: false,
}