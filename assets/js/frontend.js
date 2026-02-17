jQuery(function($){
  $(document).on('click', '.doctorly-buy', function(){
    const plan = $(this).data('plan');
    $.post(doctorlyDashboard.ajaxUrl, {
      action: 'doctorly_start_checkout',
      nonce: doctorlyDashboard.nonce,
      plan: plan
    }).done(function(resp){
      if(resp.success && resp.data.checkout_url){
        window.location.href = resp.data.checkout_url;
      } else {
        alert(resp.data?.message || 'Unable to start checkout.');
      }
    });
  });
});
