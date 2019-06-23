
(function($) {

  // Run a function when the page is fully loaded including graphics.
  $(window).load(function() {
    // Checks the email when the download button is clicked.
    $('#download-btn').click( function(event) { if($.fn.checkEmail()) { $('#collapseModal').modal('toggle');} else { event.preventDefault();} });
  });

  $.fn.checkEmail = function() {
    // Checks the given email.
    let email = $('#email').val();
    let testEmail = /^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,}$/i;

    if(testEmail.test(email)) {
      // Retrieves the download url.
      let link = $('#download-btn').attr('href');
      // Adds the email address variable at the end of the download url.
      $('#download-btn').attr('href', link+'&email='+encodeURIComponent(email));

      return true;
    }
    // Invalid email.
    else {
      $('#email').addClass('required invalid');
      alert(Joomla.JText._('COM_OKEYDOC_INVALID_EMAIL'));

      return false;
    }
  }

})(jQuery);

