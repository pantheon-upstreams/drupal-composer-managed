!function(){var r;r=jQuery,Drupal.behaviors.accordion={attach:function(a){r(".c-accordion",a).each(function(a,t){r(this).find("button[aria-expanded]").each(function(a,t){r(this).click(function(){var a=r(this);("true"===a.attr("aria-expanded")?(a.attr("aria-expanded","false"),r("#".concat(a.attr("aria-controls"))).attr("hidden","true"),a):(a.attr("aria-expanded","true"),r("#".concat(a.attr("aria-controls"))))).removeAttr("hidden")})})})}}}();