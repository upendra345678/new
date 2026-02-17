jQuery(function($){
  $(document).on('click','.doctorly-buy',function(){
    const plan=$(this).data('plan');
    $.post(doctorlyFrontend.ajaxUrl,{action:'doctorly_package_checkout',nonce:doctorlyFrontend.nonce,plan:plan},function(res){
      if(res.success&&res.data.redirect){window.location.href=res.data.redirect;}else{alert(res.data?.message||'Unable to continue');}
    });
  });
});
