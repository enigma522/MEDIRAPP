const ajout = document.getElementById("ajouter");
if (ajout)
  ajout.addEventListener("click", function (e) {
    document.querySelector(".popupx").style.display = "flex";
  });

document.getElementById("close").addEventListener("click", function (e) {
  document.querySelector(".popupx").style.display = "none";
});

document.querySelector("#add").addEventListener("click", function (e) {
  e.preventDefault();
  email = document.querySelector("#addmail").value;
  console.log(email);
  document.querySelector("#addmail").value = "";
  $.ajax({
    url: "http://localhost:8000/add_patient/" + email,
    datatype: "json",
    success: function (data) {
      document.querySelector(".popupx").style.display = "none";
      console.log(data);
    },
  });
});
