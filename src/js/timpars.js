var server = 'https://proted.sd.di.huc.knaw.nl/timpars/';
var login_server = 'https://secure.huygens.knaw.nl/saml2/login';
var obj;
var active_uri;

function edit_simple_person(nr) {
    resetEditor();
    editor = document.createElement('div');
    editor.setAttribute('id', 'ccform');
    $("#editBtn_" + nr).parent().append(editor);
    $("#editBtn_" + nr);
    $("#editBtn_" + nr).hide();
    getSimplePerson(nr);
}

function edit_interest_field(nr) {
    resetEditor();
    editor = document.createElement('div');
    editor.setAttribute('id', 'ccform');
    $("#editBtn_" + nr).parent().append(editor);
    $("#editBtn_" + nr);
    $("#editBtn_" + nr).hide();
    submitButton.actionURI = server + 'examples/set_fields';
    saveButton.actionURI = submitButton.actionURI;
    getInterestField(nr);
}

function addCollection(){
    alert('Link to backend to add a collection');
}

function getSimplePerson(nr){
    uri = $("#uri_" + nr).val(); 
    active_uri = uri;
    obj = getEditableObject(uri, 'simple_person', true);
    return obj;
}

function getInterestField(nr){
    uri = $("#uri_" + nr).val(); 
    active_uri = uri;
    obj = getEditableObject(uri, 'interest_field', true);
    return obj;
}

function getEditableObject(uri, objectType, single) {
    $.ajax( {
       type: "POST",
       url: server + 'examples/get_editable_object',
       data: {
          uri: uri,
          type: objectType,
          single: true
       },
       success: function (result) {
           obj = JSON.parse(result);
           create_tim_form();
       },
       error: function () {
           alert('error');
       }
    });
}

function create_tim_form() {
    formBuilder.start(obj);
}

function resetEditor() {
    document.location = server;
}

function drop_dataset(id){
    if (confirm('Drop dataset ' + id + '?')) {
        window.location = server + 'drop/dataset/' + id;
    }
}

function publish_dataset(id) {
    if (confirm('Publish dataset?')) {
        window.location = server + 'publish/' + id;
    }
}

function drop_collection_item(dataset, collection, cUri){
    if (confirm('Drop collection item?')) {
        window.location = server + 'drop/item/' + dataset + '/' + collection + '/' + cUri;
    }
}

function login() {
var lform = document.createElement('form');
lform.action = login_server;
lform.id = 'loginForm'
lform.method = 'POST';
var field = document.createElement('input');
field.type = 'hidden';
field.name = 'hsurl';
field.value = document.location;
lform.append(field);
document.getElementById('loginFormDiv').append(lform);
document.getElementById('loginForm').submit();
}


