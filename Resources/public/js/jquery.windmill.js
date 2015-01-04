// the semi-colon before the function invocation is a safety
// net against concatenated scripts and/or other plugins
// that are not closed properly.
;
(function ($, window, document, undefined) {

  // undefined is used here as the undefined global
  // variable in ECMAScript 3 and is mutable (i.e. it can
  // be changed by someone else). undefined isn't really
  // being passed in so we can ensure that its value is
  // truly undefined. In ES5, undefined can no longer be
  // modified.

  // window and document are passed through as local
  // variables rather than as globals, because this (slightly)
  // quickens the resolution process and can be more
  // efficiently minified (especially when both are
  // regularly referenced in your plugin).

  // Create the defaults once
  var pluginName = "windmill",
    defaults = {
      url: 'http://my-site.org/windmill/game/1234/move',
      ajax_toggle: '#WindmillAjax',
      form_from: '#WindmillFrom',
      form_to: '#WindmillTo'
    };

  // The actual plugin constructor
  function Plugin(element, options) {
    this.element = element;
    this.$element = $(element);

    // jQuery has an extend method that merges the
    // contents of two or more objects, storing the
    // result in the first object. The first object
    // is generally empty because we don't want to alter
    // the default options for future instances of the plugin
    this.options = $.extend({}, defaults, options);

    this._defaults = defaults;
    this._name = pluginName;

    this.init();
  }

  Plugin.prototype = {
    init: function () {
      // Place initialization logic here
      // You already have access to the DOM element and
      // the options via the instance, e.g. this.element
      // and this.options
      // you can add more functions like the one below and
      // call them like so: this.yourOtherFunction(this.element, this.options).

      var self = this;

      self.ajaxToggle = self.$element.find(self.options.ajax_toggle);
      self.formFrom = self.$element.find(self.options.form_from);
      self.formTo = self.$element.find(self.options.form_to);

      self.$element.find('.square').each(function (i, el) {
        self.initSquare($(el));
      });

      self.$element.find('.square').click(function() {
        var to = $(this);
        if (to.hasClass('target')) {
          var from = self.$element.find('.square.selected').eq(0);
          if (from.length < 1) {
            console.log('No initial square selected');
          }

          if (self.ajaxToggle.is(':checked')) {
            $.ajax({
              url: self.options.url,
              method: 'post',
              data: {
                from: from.data('position'),
                to: to.data('position')
              },
              windmill: self,
              success: function (data) {
                this.windmill.handleResponse(data);
              }
            });
          } else {
            // just submit (hidden) form
            self.formFrom.attr('value', from.data('position'));
            self.formTo.attr('value', to.data('position'));

            self.formFrom.closest('form').submit();
          }
        }
      });
    },

    handleResponse: function (data) {
      this.removeSelections();

      if (!data.ok) {
        return;
      }

      for (var x = 0; x < data.result.squares.length; x++) {
        var squareData = data.result.squares[x];
        var square = this.getSquare(squareData.position);
        square.data('piece-type', squareData.piece_type);
        square.data('piece-color', squareData.piece_color);
        square.data('possible-targets', squareData.possible_targets.join(','));
        square.html(squareData.content);

        this.initSquare(square);
      }
    },

    getSquare: function (position) {
      return this.$element.find('.square[data-position='+position+']');
    },

    initSquare: function (el) {
      var self = this;
      el.css('cursor', 'inherit');
      if (el.data('possible-targets')) {
        el.css('cursor', 'pointer');
        var possibleTo = el.data('possible-targets').toString().split(',');
        el.click(function(e) {
          self.removeSelections();
          el.addClass('selected');
          for (var x = 0; x < possibleTo.length; x++) {
            var toSquare = self.getSquare(possibleTo[x]);
            toSquare.addClass('target');
            toSquare.css('cursor', 'pointer');
          }
        });
      }
    },

    removeSelections: function () {
      this.$element.find('.square.selected').removeClass('selected');
      this.$element.find('.square.target').removeClass('target');
    }
  };

  // A really lightweight plugin wrapper around the constructor,
  // preventing against multiple instantiations
  $.fn[pluginName] = function (options) {
    return this.each(function () {
      if (!$.data(this, "plugin_" + pluginName)) {
        $.data(this, "plugin_" + pluginName,
          new Plugin(this, options));
      }
    });
  };

})(jQuery, window, document);
