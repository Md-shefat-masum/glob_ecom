/**
 * Theme: Drezoc - Bootstrap 4 Admin Template
 * Author: Myra Studio
 * File: Main Js
 */

!(function (t) {
  "use strict";
  t("#side-menu").metisMenu(),
    t("#vertical-menu-btn").on("click", function () {
      t("body").toggleClass("enable-vertical-menu");
    }),
    t(".menu-overlay").on("click", function () {
      t("body").removeClass("enable-vertical-menu");
    }),
    // t("#sidebar-menu a").each(function () {
    //   var a = window.location.href.split(/[?#]/)[0];
    //   this.href == a &&
    //     (t(this).addClass("active"),
    //     t(this).parent().addClass("mm-active"),
    //     t(this).parent().parent().addClass("mm-show"),
    //     t(this).parent().parent().prev().addClass("mm-active"),
    //     t(this).parent().parent().parent().addClass("mm-active"),
    //     t(this).parent().parent().parent().parent().addClass("mm-show"),
    //     t(this)
    //       .parent()
    //       .parent()
    //       .parent()
    //       .parent()
    //       .parent()
    //       .addClass("mm-active"));
    // }),
    t("#sidebar-menu a").each(function () {
      var currentUrl = window.location.href.split(/[?#]/)[0];
      var $link = t(this);

      // Check if the link has a data-active-paths attribute
      var activePaths = $link.data("active-paths");
      if (activePaths) {
        var paths = activePaths.split(",");

        // Check if any of the paths match the current URL (exact or pattern match)
        for (var i = 0; i < paths.length; i++) {
          var path = paths[i].trim();

          // Convert wildcard to regex if contains "*"
          var regex = new RegExp("^" + path.replace(/\*/g, ".*") + "$");
          if (regex.test(currentUrl)) {
            $link.addClass("active");
            $link.parent().addClass("mm-active");
            $link.parent().parent().addClass("mm-show");
            $link.parent().parent().prev().addClass("mm-active");
            $link.parent().parent().parent().addClass("mm-active");
            $link.parent().parent().parent().parent().addClass("mm-show");
            $link
              .parent()
              .parent()
              .parent()
              .parent()
              .parent()
              .addClass("mm-active");
            break;
          }
        }
      }
    });

  t(function () {
    t('[data-toggle="tooltip"]').tooltip();
  }),
    t(function () {
      t('[data-toggle="popover"]').popover();
    });
})(jQuery);


function check_demo_user() {
  return 0;
}

function show_errors(errors) {
  // Clear previous error messages first
  clear_error();

  // Loop through error object
  Object.keys(errors).forEach(function (field) {
    const messages = errors[field];
    const input = document.querySelector(`[name="${field}"]`);

    if (input && messages && messages.length > 0) {
      const msg = document.createElement('div');
      msg.classList.add('error_msg');
      msg.classList.add('text-danger');
      msg.textContent = messages[0]; // show first error only
      input.parentElement.insertAdjacentElement('afterend', msg);
    }
    toastr.error(messages[0]);
  });
}

function clear_error() {
  document.querySelectorAll('.error_msg').forEach(el => el.remove());
}