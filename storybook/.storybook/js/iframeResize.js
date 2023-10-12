(function () {
  console.log('loads')
  // Observe the entire document (including the body)
  iframeObserver.observe(document, {
    childList: true,
    subtree: true,
  });

  /**
   * ResizeObserver callback that resizes the parent iframe based on
   * the height of the child document html element.
   *
   * @param {ResizeObserverEntry[]} entries
   */
  function onBodyResize(entries) {
    console.log('resize');
    // Resize the parent iframe based on the html's border box height.
    if (window.frameElement && entries.length) {
      window.frameElement.height = entries[0].borderBoxSize[0].blockSize + 1;
    }
  }

  // Observe changes to the <html> dimensions.
  new ResizeObserver(onBodyResize).observe(document.documentElement, { box: 'border-box'});
})();
