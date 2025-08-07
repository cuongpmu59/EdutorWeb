$(document).ready(function () {
    $(document).on('click', '.thumb', function () {
      const src = $(this).attr('src');
      if (src) {
        $('#imgModalContent').attr('src', src);
        $('#imgModal').fadeIn();
      }
    });
  
    $('#imgModal').on('click', function () {
      $(this).fadeOut();
    });
  });
  