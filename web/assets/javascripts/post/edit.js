$(document).ready(function () {
  var savePost = function () {
    var $autosaveAlert = $('#autosave-alert');

    var $form = $('form[name="appbundle_post"]');

    var serializedForm = $form.serialize();

    var url = $form.data('autosave-url');

    var jqxhr = $.post(url, serializedForm, function(response) {
      $autosaveAlert.html('Your changes are saved automatically every 10 seconds.');
      $autosaveAlert.addClass('alert-info');
    })
      .fail(function() {
        $autosaveAlert.html('Cannot connect to server. Your changes are not saved automatically. Check your internet connection or contact us for help.');
        $autosaveAlert.addClass('alert-danger');
      })
  };

  setInterval(savePost, 10000);
});
