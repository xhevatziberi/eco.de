class VisualHandlerClass extends elementorModules.frontend.handlers.Base {

    getDefaultSettings() {
      if ( this.isEdit ) {
        fixElements();
      }
    }

    onElementChange(e) {
      console.log('onElementChange');
      fixElements();
    }
}

jQuery( window ).on( 'elementor/frontend/init', () => {
  // fixElements()

   elementorFrontend.hooks.addAction( 'frontend/element_ready/page-header.default', addHandler );
   elementorFrontend.hooks.addAction( 'frontend/element_ready/visual-test.default', addHandler );
   elementorFrontend.hooks.addAction( 'frontend/element_ready/visual-content.default', addHandler );
});

function fixElements() {
  console.log('fixElements');
}
