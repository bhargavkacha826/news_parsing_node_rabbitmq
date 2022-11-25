$(function () {
  $("body").on("click", ".delete_news", function (event) {
    event.stopImmediatePropagation();
    var id = $(this).attr("data-id");
    if (confirm("Are you sure you want to perform this action?")) {
      $('.loading').show();
      $.ajax({
        url: "/admin/makeAction",
        data: { id: id, action: "delete_news" },
        type: "POST",
        success: function (res) {
          $('.loading').hide();
          alert("News Deleted SuccessFully!");
          location.reload();
        },
      });
    }
  });
});
