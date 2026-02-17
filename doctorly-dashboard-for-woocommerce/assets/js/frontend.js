jQuery(function ($) {
  $(document).on('click', '.doctorly-buy', function () {
    const button = $(this);
    button.prop('disabled', true).text('Processing...');

    $.post(DoctorlyDashboard.ajaxUrl, {
      action: 'doctorly_start_checkout',
      nonce: DoctorlyDashboard.nonce,
      plan_slug: button.data('plan')
    }).done(function (response) {
      if (response.success && response.data.redirect) {
        window.location.href = response.data.redirect;
        return;
      }
      alert(response.data.message || 'Unable to start checkout.');
      button.prop('disabled', false).text('Buy / Upgrade');
    }).fail(function () {
      alert('Checkout failed.');
      button.prop('disabled', false).text('Buy / Upgrade');
    });
  });
});
