/**
 * @file
 * Profile presets and configuration UI.
 */
(function ($, Drupal, drupalSettings) {

  'use strict';

  /**
   * Handler for switching between profiles.
   */
  Drupal.behaviors.openy_profile_preset = {
    attach: function (context, settings) {
      var $form = $('.openy-configure-profile', context);
      if ($form.length < 1) {
        return;
      }

      var $preset_elem = $('#edit-preset', $form);
      var $checkboxes = $(':checkbox', $form);
      $preset_elem
        .on('change', function (e) {
          var value = $(this).val();
          if (value !== 'custom') {
            $checkboxes.prop('checked', false);
            $(settings.presets[value]).each(function (key, value) {
              $checkboxes.filter('[value="' + value + '"]').prop('checked', true);
            });
          }
        })
        .trigger('change');

      var $details = $('details', $form);
      $details.each(function (i, $elem) {
        var $local_checkboxes = $(':checkbox', $elem);
        if ($local_checkboxes.length < 4) {
          return;
        }
        // Append links.
        var $wrapper = $('.details-wrapper', $elem);
        $wrapper.prepend('<div class="helper-links">' +
          '<a href="#" class="helper-link-select-all">Select all</a> or ' +
          '<a href="#" class="helper-link-select-none">select none</a>' +
          '</div>');
        var $link_wrapper = $wrapper.find('.helper-links');
        var $select_all = $wrapper.find('.helper-link-select-all');
        var $select_none = $wrapper.find('.helper-link-select-none');

        // Attach onclick handlers.
        $select_all.on('click', function (e) {
          $local_checkboxes.prop('checked', true);
          return false;
        });
        $select_none.on('click', function (e) {
          $local_checkboxes.prop('checked', false);
          return false;
        });
      });

      // Switch to 'custom' when checkboxes are clicked.
      $checkboxes.on('click', function (e) {
        $preset_elem.val('custom');
      });


    }
  };

})(jQuery, Drupal, drupalSettings);
