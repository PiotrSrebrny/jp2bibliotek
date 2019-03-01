function deleteAuthorField(id) {
    var toDelete = document.getElementById(id);
    if (toDelete != null)
        document.getElementById("form_authors").removeChild(toDelete);
}
function createAuthorField() {
    var id = Date.now();
    var formId = "form_author_" + id;
    var inputId = "author_" + id;
    var authorsForm = document.getElementById("form_authors");
    var label = document.createElement("label");
    label.className = "col-sm-2 control-label";
    label.innerText = "Autor";
    var input = document.createElement("input");
    input.className = "form-control";
    input.name = inputId;
    input.id = inputId;
    input.placeholder = "Autor książki";
    input.onkeyup = (function (ev) {
        lookUp(input, 'authors');
    });
    var divInput = document.createElement("div");
    divInput.className = "col-sm-4";
    divInput.appendChild(input);
    var delBtn = document.createElement("label");
    delBtn.className = "btn btn-default";
    delBtn.innerText = "Usuń";
    delBtn.addEventListener("click", function (event) {
        deleteAuthorField(formId);
    });
    var div = document.createElement("div");
    div.id = formId;
    div.className = "form-group";
    div.appendChild(label);
    div.appendChild(divInput);
    div.appendChild(delBtn);
    authorsForm.appendChild(div);
}
