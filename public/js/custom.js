$('.panel-collapse').on('hide.bs.collapse', function(e) {
  if ($(event.target).parent().hasClass('collapsed')) {
    e.stopPropagation();
    e.preventDefault();
  }
});
