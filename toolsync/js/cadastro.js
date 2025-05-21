document.getElementById("formCadastro").addEventListener("submit", function (e) {
  const termos = document.getElementById("termos").checked;
  if (!termos) {
    alert("Você deve aceitar os termos.");
    e.preventDefault();
  }
});
