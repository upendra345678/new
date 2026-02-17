jQuery(function ($) {
  $('#doctorly-upload-logo').on('click', function (e) {
    e.preventDefault();
    const frame = wp.media({ title: 'Select Logo', multiple: false });
    frame.on('select', function () {
      const attachment = frame.state().get('selection').first().toJSON();
      $('#doctorly_logo_id').val(attachment.id);
    });
    frame.open();
  });
});
