$(document).on("click", ".mc-thumbnail", function () {
  const fullSrc = $(this).data("full");
  $("#imgModalContent").attr("src", fullSrc);
  $("#imgModal").css("display", "flex");
});

// Click ra ngoài để đóng modal
$("#imgModal").on("click", function (e) {
  if (e.target.id === "imgModal" || e.target.id === "imgModalContent") {
    $(this).hide();
    $("#imgModalContent").attr("src", ""); // reset
  }
});
