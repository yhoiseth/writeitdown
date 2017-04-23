$(document).ready(function () {
  $('#appbundle_post_body').bind('input propertychange', function () {
    var $form = $('form[name="appbundle_post"]');
    var serializedForm = $form.serialize();
    var url = $form.data('autosave-url');

    $.post(url, serializedForm, function(response) {
      console.log(response);
    });
  });
});
