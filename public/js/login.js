$(document).ready(function () {
  $("#form_login").validate({
    onkeyup: false,
    rules: {
      _username: {
        required: true,
        email: true,
      },
      _password: {
        required: true,
      },
    },
    messages: {
      _username: {
        required: "Cette valeur ne doit pas être vide",
        email: "S'il vous plaît, mettez une adresse email valide.",
      },
      _password: {
        required: "Cette valeur ne doit pas être vide",
      },
    },
  });
});
