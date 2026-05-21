// Per-site visual regression scenarios. Consumed by @threespot/visual-regression.
//
// Each scenario gets captured at every (browser x viewport) combination the
// shared package configures. Paths must start with "/" and are appended to
// VRT_BASELINE_URL / VRT_TEST_URL at runtime.
//
// See `node_modules/@threespot/visual-regression/example/scenarios.js` for
// the full schema, including the object form for site-wide overrides
// (custom viewports, site-wide masks, beforeScreenshot hook).

module.exports = [
  { label: "Homepage", path: "/" },
  // { label: "Sample Post", path: "/sample-post/" },
  // { label: "Blog Index", path: "/blog/", masks: [".post-list time"] },
];
