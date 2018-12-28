function colorPickerInit(elem = null) {
  var selector = 'select.color-picker';
  var pickers = elem ? $(elem).find(selector) : $(selector);

  pickers.each(function(i, picker) {
    $(picker).find('option').each(function(j, option) {
      $(option).css('background-color', option.value || '');
    });
  });

  pickers.change(function() {
    $(this).parent().css('background-color', this.value || '');
  });

  pickers.trigger('change');
}

$.fn.colorPickers = function() {
  colorPickerInit(this);
};

$(document).ready(function() {
  colorPickerInit();
});
