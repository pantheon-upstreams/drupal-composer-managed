(function ($, Drupal) {
  Drupal.behaviors.ariaControls = {
    attach: function (context) {
      once("ariaControls", "#block-surf-main-main-menu", context).forEach(
        function (menu) {
          // Find top level menu buttons
          const buttons = $(menu).find(
            "ul[data-depth='0'].menu--main > li > button.menu__link"
          );
          // Add aria controls for buttons & related submenus
          // For each primary level for main menu, attach aria label
          buttons.each(function () {
            const id = $(this).text().toLowerCase().replace(/\s/g, "");
            $(this)
              .attr("aria-haspopup", "true")
              .attr("aria-controls", `pannel-${id}`)
              .next()
              .attr("id", `pannel-${id}`)
              .attr("aria-label", $(this).text());
          });
        }
      );
    },
  };
})(jQuery, Drupal);
