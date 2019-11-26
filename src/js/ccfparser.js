var objectDisplay = true;
var objectLevel = 1;
var validationProfiles = {};
var inputOK = true;
var isUploading = false;
var cloneBuffer;
var panelError;
var recordEdit = false;


var formBuilder = {
    profileID: null,
    start: function (obj) {
        objectDisplay = true;
        objectLevel = 1;
        validationProfiles = {};
        inputOK = true;
        isUploading = false;
        recordEdit = false;
        //this.profileID = obj.id;
        this.profileID = active_uri;
        this.parse(obj.content);
        this.createButtons();
        createAutoCompletes();
        if (obj.record !== undefined) {
            fillValues(obj.record);
            $(".disabledElement").attr("class", "element");
            $(".disabledComponent").addClass("component").removeClass("disabledComponent");
            $(".optionalCompBtn").click();
        }
    },
    parse: function (o, componentID) {
        if (o.hasOwnProperty('type')) {
            switch (o.type) {
                case 'Element':
                    this.handleElement(o, componentID);
                    break;
                case 'Component':
                    this.handleComponent(o, componentID);
                    this.parse(o.content, o.ID);
                    break;
                default:
                    alert('Error: property type not defined');
                    break;
            }
        } else {
            for (var key in o) {
                var type = typeof o[key];
                if (type === 'object') {
                    this.parse(o[key], componentID);
                }
            }
        }
    },
    handleElement: function (element, componentID) {
        html = document.createElement('div');
        if (objectDisplay) {
            html.setAttribute('class', 'element');
        } else {
            html.setAttribute('class', 'disabledElement');
        }
        html.setAttribute('data-name', element.attributes.name);
        html.setAttribute('data-order', element.attributes.initialOrder);
        label = document.createElement('div');
        label.setAttribute('class', 'label');
        if (element.attributes.CardinalityMin > 0) {
            label.innerHTML = element.attributes.label + ' *';
        } else {
            label.innerHTML = element.attributes.label;
        }
        if (element.attributes.ValueScheme === 'date') {
            label.innerHTML = label.innerHTML + ' (' + ccfOptions.alert.date_string + ')';
        }
        input = document.createElement('div');
        input.setAttribute('class', 'control');
        control = this.createControl(element);
        input.appendChild(control);
        if (element.attributes.Multilingual === 'true') {
            var dropdown = document.createElement('select');
            dropdown.setAttribute('class', 'language_dd');
            dropdown.setAttribute('id', 'lang_' + element.ID);
            var option = document.createElement('option');
            option.setAttribute('value', 'none');
            option.innerHTML = '--';
            dropdown.appendChild(option);
            for (var key in this.languages) {
                var option = document.createElement('option');
                option.setAttribute('value', this.languages[key]);
                option.innerHTML = this.languages[key];
                dropdown.appendChild(option);
            }
            $(dropdown).val(ccfOptions.language);
            input.appendChild(dropdown);
        }
        if (element.attributes.duplicate !== undefined)
        {
            var btn = document.createElement('input');
            btn.setAttribute('type', 'button');
            btn.setAttribute('value', '+');
            btn.setAttribute('class', 'btn');
            btn.setAttribute('data-source', element.ID);
            btn.onclick =
                    function (e) {
                        var next = clone.nextClonePostfix();
                        var that = $(this);
                        var tempID;
                        e.preventDefault();
                        clonedElement = that.parent().clone();
                        clonedElement.attr('class', 'clone');
                        clonedElement.find(".btn").each(
                                function () {
                                    $(this).attr('value', '-');
                                    $(this).on("click", function (e) {
                                        e.preventDefault();
                                        var that = $(this);
                                        that.parent().remove();
                                    });
                                });
                        clonedElement.find("#" + that.attr('data-source')).each(
                                function () {
                                    var id = $(this).attr('id');
                                    $(this).val("");
                                    tempID = id + '_' + next;
                                    $(this).attr('id', tempID);
                                });
                        clonedElement.find(".errorMsg").each(
                                function () {
                                    var id = $(this).attr('id');
                                    $(this).attr('id', id + '_' + next);
                                    $(this).html("");
                                });
                        clonedElement.find(".language_dd").each(
                                function () {
                                    $(this).attr('id', 'lang_' + tempID);
                                    $(this).val(ccfOptions.language);
                                });
                        clonedElement.insertAfter(that.parent());
                        createAutoCompletes();
                    };
            input.appendChild(btn);
        }
        html.appendChild(label);
        var Msg = document.createElement('div');
        Msg.setAttribute("id", "errorMsg_" + element.ID);
        Msg.setAttribute('class', 'errorMsg');
        input.appendChild(Msg);
        html.appendChild(input);
        if (element.attributes.hide === 'yes') {
            $(html).addClass("hidden_element");
        }
        $("#" + componentID).append(html);
        validationProfiles[element.ID] = element;
    },
    handleComponent: function (component, componentID) {
        html = document.createElement('div');
        if (objectDisplay || Number(component.level) <= Number(objectLevel)) {
            html.setAttribute('class', 'component');
        } else {
            html.setAttribute('class', 'disabledComponent');
        }
        html.setAttribute('id', component.ID);
        html.setAttribute('data-name', component.attributes.name);
        html.setAttribute('data-order', component.attributes.initialOrder);
        header = document.createElement('div');
        header.setAttribute('class', 'componentHeader');
        header.innerHTML = component.attributes.label;
        if (component.attributes.CardinalityMin === '0')
        {
            var btn = document.createElement('input');
            btn.setAttribute('type', 'button');
            btn.setAttribute('value', "✓");
            btn.setAttribute('class', 'optionalCompBtn');
            $(btn).on("click", showComponentFields);
            header.appendChild(btn);
            if (objectDisplay) {
                objectDisplay = false;
                objectLevel = component.level;
            }

        } else {
            objectDisplay = true;
        }

        if (component.attributes.CardinalityMax !== '1')
        {
            //header.innerHTML = component.attributes.label;
            var btn = document.createElement('input');
            btn.setAttribute('type', 'button');
            btn.setAttribute('value', '+');
            btn.setAttribute('class', 'compBtn');
            btn.setAttribute('data-source', component.ID);
            if (component.attributes.resource !== undefined || component.attributes.CardinalityMin === '0') {
                $(btn).hide();
            }
            btn.onclick = function (e) {
                var next = clone.nextClonePostfix();
                var that = $(this);
                e.preventDefault();
                clonedComponent = that.parent().parent().clone();
                clonedComponent.addClass("clonedComponent");
                clonedComponent.attr("id", clonedComponent.attr("id") + '_' + next);
                clonedComponent.find(".compBtn").each(
                        function () {
                            $(this).attr('value', '-');
                            $(this).on("click", function (e) {
                                e.preventDefault();
                                var that = $(this);
                                that.parent().parent().remove();
                            });
                        });
                clonedComponent.find(".clone").each(
                        function () {
                            $(this).remove();
                        });
                clonedComponent.find(".errorMsg").each(
                        function () {
                            $(this).html('');
                            var id = $(this).attr('id');
                            $(this).attr('id', id + '_' + next);
                        });
                clonedComponent.find(".input_element").each(function () {
                    var id = $(this).attr("id");
                    $(this).attr('id', id + '_' + next);
                    $(this).val("");
                });
                clonedComponent.find(".uploader").each(function () {
                    var id = $(this).attr("id");
                    $(this).attr('id', id + '_' + next);
                    $(this).val("");
                    $(this).show();
                    $(this).on("change", function () {
                        addUploadTrigger(this);
                    });
                });
                clonedComponent.find(".headerMsg").remove();
                clonedComponent.find(".optionalCompBtn").remove();
                clonedComponent.attr("data-filename", null);
                //that.parent().parent().parent().append(clonedComponent);
                clonedComponent.insertAfter(that.parent().parent());
                addAutoComplete(clonedComponent);
            };
            header.appendChild(btn);
        }
        if (component.attributes.resource !== undefined) {
            var form = document.createElement('form');
            form.setAttribute("method", "post");
            form.setAttribute("class", "fileForm");
            var fileSelector = document.createElement('input');
            fileSelector.setAttribute('type', 'file');
            fileSelector.setAttribute('id', 'files_' + component.ID);
            fileSelector.setAttribute("class", "uploader");
            checkResourceFilter(fileSelector, component.attributes.resource);
            if (component.attributes.CardinalityMin === '0') {
                $(fileSelector).hide();
            }
            fileSelector.onchange = function () {
                addUploadTrigger(this);
            };
            form.appendChild(fileSelector);
            var resetUpload = document.createElement('input');
            resetUpload.setAttribute("type", "reset");
            resetUpload.setAttribute("target", component.ID);
            resetUpload.setAttribute("value", "x");
            resetUpload.setAttribute("class", "resetUploadBtn");
            resetUpload.onclick = function () {
                var target = $(this).attr("target");
                $("#files_" + target).show();
                $("#" + target).find(".headerMsg").remove();
                $("#" + target).removeAttr("data-filename");
                $(this).hide();
            }
            $(resetUpload).hide();
            form.appendChild(resetUpload);
            header.appendChild(form);
        }
        html.appendChild(header);
        if (componentID === undefined)
        {
            $("#ccform").append(html);
        }
        else {
            $("#" + componentID).append(html);
        }
    },
    createControl: function (element) {
        var type = typeof element.attributes.ValueScheme;
        if (type === "object") {
            control = this.setValueSchemeElement(element.attributes.ValueScheme[0], element.ID);
        }
        else {
            switch (element.attributes.ValueScheme) {
                case 'boolean':
                    var control = document.createElement('select');
                    control.setAttribute('id', element.ID);
                    control.setAttribute('data-reset-value', '');
                    var option = document.createElement('option');
                    option.setAttribute("value", '');
                    option.innerHTML = '--';
                    control.appendChild(option);
                    var option = document.createElement('option');
                    option.setAttribute("value", "true");
                    option.innerHTML = 'Ja';
                    control.appendChild(option);
                    var option = document.createElement('option');
                    option.setAttribute("value", "false");
                    option.innerHTML = 'Nee';
                    control.appendChild(option);
                    break;
                case 'string':
                case 'decimal':
                    var control = this.createTextInputField(element);
                    break;
                default:
                    var control = document.createElement('input');
                    control.setAttribute('id', element.ID);
                    control.setAttribute('type', 'text');
                    control.setAttribute('data-reset-value', 'line');
                    if (element.attributes.autoValue === undefined) {
                        control.setAttribute('value', "");
                    } else {
                        control.setAttribute('value', element.attributes.autoValue.value);
                    }

            }
        }
        control.setAttribute('data-validation-profile', element.ID);
        //control.setAttribute('data-order', element.attributes.initialOrder);
        $(control).addClass("input_element");
        return control;
    },
    setValueSchemeElement: function (element, id) {
        var control = document.createElement('select');
        control.setAttribute('id', id);
        var option = document.createElement('option');
        option.setAttribute("value", '');
        option.innerHTML = '--';
        control.appendChild(option);
        for (var key in element) {
            var option = document.createElement('option');
            option.setAttribute("value", element[key].value);
            if (element[key].label === undefined) {
                option.innerHTML = element[key].value;
            } else {
                option.innerHTML = element[key].label;
            }

            control.appendChild(option);
        }
        return control;
    },
    createTextInputField: function (element) {
        if (element.attributes.inputField === undefined) {
            var type = '';
        } else {
            var type = element.attributes.inputField;
        }
        switch (type) {
            case 'multiple':
                var control = document.createElement('textarea');
                control.setAttribute('rows', this.setInputFieldHeigth(element.attributes.heigth));
                control.setAttribute('cols', this.setInputFieldWidth(element.attributes.width));
                control.setAttribute('data-reset-value', 'area');
                control.setAttribute('id', element.ID);
                break;
            case 'single':
            default:
                var control = document.createElement('input');
                control.setAttribute('type', 'text');
                control.setAttribute('size', this.setInputFieldWidth(element.attributes.width));
                control.setAttribute('data-reset-value', 'line');
                if (element.attributes.autoValue === undefined) {
                    control.setAttribute('value', "");
                } else {
                    control.setAttribute('value', element.attributes.autoValue.value);
                }
                control.setAttribute('id', element.ID);
                if (element.attributes.autoCompleteURI !== undefined) {
                    control.setAttribute("data-auto", "yes");
                }
                break;
        }

        return control;
    },
    setInputFieldWidth: function (value) {
        if (value === undefined) {
            return 50;
        } else {
            return value;
        }
    },
    setInputFieldHeigth: function (value) {
        if (value === undefined) {
            return 8;
        } else {
            return value;
        }
    },
    createResourcePanel: function () {
        var resourceFrame = document.createElement('div');
        resourceFrame.setAttribute('id', 'resourceFrame');
        resourceFrame.setAttribute('class', 'component');
        header = document.createElement('div');
        header.setAttribute('class', 'componentHeader');
        header.innerHTML = 'Bestanden';
        var btn = document.createElement('input');
        btn.setAttribute('type', 'button');
        btn.setAttribute('value', '+');
        btn.setAttribute('class', 'compBtn');
        btn.setAttribute('id', 'resourceBtn');
        btn.onclick = function (e) {
            var that = $(this);
            e.preventDefault();
            var list = that.parent().parent();
            var input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('class', 'filename');
            input.setAttribute("id", "resList");
            input.setAttribute('multiple', "");
            list.append(input);
        };
        header.appendChild(btn);
        resourceFrame.appendChild(header);
        $("#ccform").append(resourceFrame);
    },
    createButtons: function () {
        var buttonFrame = document.createElement('div');
        buttonFrame.setAttribute('id', 'btnFrame');
        var control = document.createElement('input');
        control.setAttribute('type', 'button');
        control.setAttribute('value', ccfOptions.submitButton.label);
        control.setAttribute('id', 'OKbtn');
        control.onclick = function () {
            validate();
        };
        buttonFrame.appendChild(control);
//        var control = document.createElement('input');
//        control.setAttribute('type', 'button');
//        control.setAttribute('value', ccfOptions.saveButton.label);
//        control.setAttribute('id', 'saveBtn');
//        control.onclick = function () {
//            sendForm();
//        };
//        buttonFrame.appendChild(control);
        if (ccfOptions.resetButton !== null && ccfOptions.resetButton !== undefined) {
            var control = document.createElement('input');
            control.setAttribute('type', 'button');
            control.setAttribute('value', ccfOptions.resetButton.label);
            control.setAttribute('id', 'resetBtn');
            control.onclick = function () {
                resetEditor();
            };
            buttonFrame.appendChild(control);
        }
        var errorSpace = document.createElement('div');
        errorSpace.setAttribute("id", "errorSpace");
        buttonFrame.appendChild(errorSpace);
        $("#ccform").append(buttonFrame);
    },
    languages: ['aa', 'ab', 'ae', 'af', 'ak', 'am', 'an', 'ar', 'as', 'av', 'ay', 'az', 'ba', 'be', 'bg', 'bh', 'bi', 'bm', 'bn', 'bo', 'br', 'bs', 'ca', 'ce', 'ch', 'co', 'cr', 'cs', 'cu', 'cv', 'cy', 'da', 'de', 'dv', 'dz', 'ee', 'el', 'en', 'eo', 'es', 'et', 'eu', 'fa', 'ff', 'fi', 'fj', 'fo', 'fr', 'fy', 'ga', 'gd', 'gl', 'gn', 'gu', 'gv', 'ha', 'he', 'hi', 'ho', 'hr', 'ht', 'hu', 'hy', 'hz', 'ia', 'id', 'ie', 'ig', 'ii', 'ik', 'io', 'is', 'it', 'iu', 'ja', 'jv', 'ka', 'kg', 'ki', 'kj', 'kk', 'kl', 'km', 'kn', 'ko', 'kr', 'ks', 'ku', 'kv', 'kw', 'ky', 'la', 'lb', 'lg', 'li', 'ln', 'lo', 'lt', 'lu', 'lv', 'mg', 'mh', 'mi', 'mk', 'ml', 'mn', 'mr', 'ms', 'mt', 'my', 'na', 'nb', 'nd', 'ne', 'ng', 'nl', 'nn', 'no', 'nr', 'nv', 'ny', 'oc', 'oj', 'om', 'or', 'os', 'pa', 'pi', 'pl', 'ps', 'pt', 'qu', 'rm', 'rn', 'ro', 'ru', 'rw', 'sa', 'sc', 'sd', 'se', 'sg', 'si', 'sk', 'sl', 'sm', 'sn', 'so', 'sq', 'sr', 'ss', 'st', 'su', 'sv', 'sw', 'ta', 'te', 'tg', 'th', 'ti', 'tk', 'tl', 'tn', 'to', 'tr', 'ts', 'tt', 'tw', 'ty', 'ug', 'uk', 'ur', 'uz', 've', 'vi', 'vo', 'wa', 'wo', 'xh', 'yi', 'yo', 'za', 'zh', 'zu'],
    controlHash: []
};

var clone = {
    clonePostfix: 0,
    nextClonePostfix: function () {
        this.clonePostfix++;
        return this.clonePostfix;
    }
};


function showComponentFields() {
    var that = $(this);
    var comp = that.parent().parent();
    var header = that.parent();
    that.attr('value', "✗");
    $(comp).children().each(function () {
        switch ($(this).attr("class")) {
            case 'disabledComponent':
                $(this).addClass("component").removeClass("disabledComponent");
                break;
            case 'disabledElement':
                $(this).addClass("element").removeClass("disabledElement");
                break;
        }
    });
    header.find(".compBtn").show();
    header.find(".uploader").show();
    $(this).on("click", hideComponentFields);
}

function checkResourceFilter(sel, filter) {
    if (sel !== 'all') {
        $(sel).attr('accept', filter);
    }
}

function hideComponentFields() {
    var that = $(this);
    var comp = that.parent().parent();
    var header = that.parent();
    $(comp).children().each(function () {
        switch ($(this).attr("class")) {
            case 'component':
                $(this).attr("class", "disabledComponent");
                break;
            case 'element':
                $(this).attr("class", "disabledElement");
                break;
        }
    });
    //comp.attr("data-filename",null);
    //header.find(".fileForm").reset();
    //comp.siblings(".clonedComponent").remove();
    header.find(".compBtn").hide();
    header.find(".uploader").hide();
    $(this).attr("value", "✓");
    $(this).on("click", showComponentFields);
}

function createAutoCompletes() {
    $("input[data-auto='yes']").each(function () {
        $(this).devbridgeAutocomplete({
            serviceUrl: server + 'proxy.php',
            dataType: 'text',
            paramName: 'q'
        });
    });
}

function addAutoComplete(clonedComponent) {
    clonedComponent.find("input[data-auto='yes']").each(function () {
        $(this).devbridgeAutocomplete({
            serviceUrl: server + 'proxy.php',
            dataType: 'text',
            paramName: 'q'
        });
    });
}

function addUploadTrigger(obj) {
    var that = $(obj);
    that.parent().parent().find(".headerMsg").remove();
    var msg = document.createElement('div');
    msg.setAttribute("id", "msg" + that.attr("id"));
    msg.setAttribute("class", "headerMsg");
    msg.innerHTML = 'Uploading...';
    //console.log(ccfOptions.uploadButton.actionURI);
    that.parent().parent().append(msg);
    var formdata = new FormData();
    if ($(obj).prop('files').length > 0) {
        file = $(obj).prop('files')[0];
        formdata.append("file", file);
        $.ajax({
            url: ccfOptions.uploadButton.actionURI,
            type: "POST",
            data: formdata,
            processData: false,
            contentType: false,
            success: function (result) {
                that.parent().parent().parent().attr("data-filename", file.name);
                that.parent().parent().find(".headerMsg").html(file.name);
                that.parent().parent().find(".resetUploadBtn").show();
                that.hide();
            },
            error: function (result) {
                console.log(result);
                that.parent().parent().find(".headerMsg").html('ERROR!');
            }
        });
    }
}

function validate() {
    $("#errorSpace").html("");
    panelError = document.createElement("div");
    inputOK = true;
    for (var key in validationProfiles) {
        switch (getInputType($("#" + key))) {
            case "input":
                validateInput(key);
                break;
            case "textarea":
                validateTextArea(key);
                break;
            case "select":
                validateSelect(key);
                break;
            default:
                alert("Error: unknown input type!");
                inputOK = false;
                break;
        }
    }
    if (inputOK) {
        sendForm();
    } else {
        $("#errorSpace").append(errorSpace);
    }
}
;

function sendForm() {
    var formValues = [];
    $(".clonedComponent").each(function () {
        $(this).attr("class", "component");
    });
    $(".hidden_element").each(function () {
        $(this).removeClass("hidden_element");
    });
    $("#ccform").children().each(function () {
        if ($(this).attr("class") === "component") {
            var element = {};
            element.name = $(this).attr("data-name");
            element.type = 'component';
            element.sortOrder = 0;
            element.content = parseComponent(this);
            formValues.push(element);
        }
    });
    var ret = JSON.stringify(formValues);
    var form = document.createElement('form');
    $(form).attr('id', 'ccSendForm');
    $(form).attr('method', 'post');
    $(form).attr('action', ccfOptions.saveButton.actionURI);
    var inputField = document.createElement('input');
    $(inputField).attr('type', 'hidden');
    $(inputField).attr('name', 'ccData');
    $(inputField).val(ret);
    $(form).append(inputField);
    var inputField = document.createElement('input');
    $(inputField).attr('type', 'hidden');
    $(inputField).attr('name', 'ccProfileID');
    $(inputField).val(formBuilder.profileID);
    $(form).append(inputField);
    $("#ccform").append(form);
    $("#OKbtn").remove();
    $("#ccSendForm").submit();
    //console.log(ret);
}

function fillValues(record) {
    
    obj = record[2].value;
    parseRecord(obj, null);
    if (record[3] !== undefined) {
        setfiles(record[3]);
    }

}

function setfiles(files) {
    for (var key in files) {
        $(".fileForm").eq(key).each(
                function () {
                    $(this).parent().parent().attr("data-filename", files[key].file);
                    var msg = document.createElement('div');
                    msg.setAttribute("id", "msg" + $(this).attr("id"));
                    msg.setAttribute("class", "headerMsg");
                    msg.innerHTML = files[key].file;
                    $(this).parent().append(msg);
                    $(this).find(".resetUploadBtn").show();
                    $(this).find("[id^=files_]").hide();
                })
    }
}

function parseComponent(component) {
    var retStruct = [];
    $(component).children().each(function () {
        if ($(this).attr("class") === "component") {
            var element = {};
            element.name = $(this).attr("data-name");
            element.type = 'component';
            element.sortOrder = $(this).attr("data-order");
            if ($(this).attr("data-filename") !== undefined) {
                element.file = $(this).attr("data-filename");
            }
            element.content = parseComponent(this);
            retStruct.push(element);
        } else {
            if ($(this).attr("class") === "element") {
                var element = {};
                element.name = $(this).attr("data-name");
                element.type = 'element';
                element.sortOrder = $(this).attr("data-order");
                element.content = parseElement(this);
                if (element.content !== "")
                {
                    retStruct.push(element);
                }

            }
        }
    });
    return retStruct;
}

function parseElement(element) {
    var retVal = [];
    $(element).find(".input_element").each(function () {
        if ($(this).is("input") || $(this).is("select") || $(this).is("textarea")) {
            var unit = {};
            unit.value = $(this).val();
            if (unit.value !== "") {
                unit.attributes = {};
                var id = $(this).attr("id");
                unit.attributes.lang = $("#lang_" + id).val();
                retVal.push(unit);
            }
        }
    });
    if (Object.keys(retVal).length > 0)
    {
        return retVal;
    }
    else {
        return "";
    }
}

function parseRecord(obj, set) {
    
    var nameStack = {};
    for (var key in obj) {
        if (obj[key] !== null) {
            //console.log(obj[key].name);
            if (nameStack[obj[key].name] === undefined) {
                nameStack[obj[key].name] = 1;
            } else {
                nameStack[obj[key].name]++;
            }
            if (obj[key].type === 'component') {
                if (nameStack[obj[key].name] > 1) {
                    duplicateComponent(obj[key], set);
                }
                if (set === null) {
                    var newSet = $("div[data-name='" + obj[key].name + "']").eq(nameStack[obj[key].name] - 1);
                } else {
                    var newSet = $(set).find("div[data-name='" + obj[key].name + "']").eq(nameStack[obj[key].name] - 1);
                }
//                if (obj[key].attributes !== undefined) {
//                    if (obj[key].attributes.ref !== undefined) {
//                        var ref = obj[key].attributes.ref;
//                        $("div[data-name='" + obj[key].name + "']").
//                    }
//                    console.log(uploads[ref]);
//            }
                parseRecord(obj[key].value, newSet);
            } else {

                if (nameStack[obj[key].name] > 1) {
                    duplicateField(obj[key], set);
                } else {

                    var name = obj[key].name;
                    //$(set).find("div[data-name='" + name + "']").find(".input_element").first().val(obj[key].value);
                    $("div[data-name='" + name + "']").find(".input_element").first().val(obj[key].value);
                    var language = getLanguage(obj[key]);
                    if (language !== 0) {
                        $(set).find("div[data-name='" + name + "']").find(".language_dd").first().val(language);
                    }
                }
            }
        }
    }
}


function getLanguage(obj) {
    if (obj.attributes !== undefined) {
        for (var key in obj.attributes) {
            if (key === 'xml:lang') {
                return obj.attributes[key];
            }
        }
    }
    return 0;
}

function duplicateField(obj, set) {
    var next = clone.nextClonePostfix();
    var language = getLanguage(obj);
    var tempID;
    var name = obj.name;
    var btn = $(set).find("div[data-name='" + name + "']").find(".btn").first();
    //console.log(btn);
    clonedElement = $(set).find("div[data-name='" + name + "']").find(".control").first().clone();
    //var btn = $(clonedElement).find(".btn").first();
    clonedElement.attr('class', 'clone');
    clonedElement.find(".btn").each(
            function () {
                $(this).attr('value', '-');
                $(this).on("click", function (e) {
                    e.preventDefault();
                    var that = $(this);
                    that.parent().remove();
                });
            });
    clonedElement.find("[id^=" + btn.attr('data-source') + "]").each(
            function () {
                var id = $(this).attr('data-validation-profile');
                $(this).val(obj.value);
                tempID = id + '_' + next;
                console.log(tempID);
                $(this).attr('id', tempID);
            });
    clonedElement.find(".errorMsg").each(
            function () {
                var id = $(this).attr('id');
                $(this).attr('id', id + '_' + next);
                $(this).html("");
            });
    clonedElement.find(".language_dd").each(
            function () {
                $(this).attr('id', 'lang_' + tempID);
                $(this).val(language);
            });
    clonedElement.insertAfter(btn.parent());
    createAutoCompletes();
}

function duplicateComponent(obj, set) {
    var next = clone.nextClonePostfix();
    clonedComponent = $(set).find("div[data-name='" + obj.name + "']").first().clone();
    clonedComponent.addClass("clonedComponent");
    clonedComponent.attr("id", clonedComponent.attr("id") + '_' + next);
    clonedComponent.find(".compBtn").each(
            function () {
                $(this).attr('value', '-');
                $(this).on("click", function (e) {
                    e.preventDefault();
                    var that = $(this);
                    that.parent().parent().remove();
                });
            });
    clonedComponent.find(".clone").each(
            function () {
                $(this).remove();
            });
    clonedComponent.find(".errorMsg").each(
            function () {
                $(this).html('');
                var id = $(this).attr('id');
                $(this).attr('id', id + '_' + next);
            });
    clonedComponent.find(".input_element").each(function () {
        var id = $(this).attr("id");
        $(this).attr('id', id + '_' + next);
        $(this).val("");
    });
    clonedComponent.find(".uploader").each(function () {
        var id = $(this).attr("id");
        $(this).attr('id', id + '_' + next);
        $(this).val("");
        $(this).on("change", function () {
            addUploadTrigger(this);
        });
    });

    clonedComponent.find(".optionalCompBtn").each(function () {
        $(this).attr('value', "✗");
        $(this).on("click", showComponentFields);
        //$(this).click();
    });
    clonedComponent.attr("data-filename", null);
    $(set).append(clonedComponent);
    addAutoComplete(clonedComponent);
}

function validateInput(key) {
    $("[data-validation-profile=" + key + "]").each(function () {
        if (validationProfiles[key].attributes.CardinalityMin === '1' && this.value === "" && $(this).parent().parent().attr("class") !== 'disabledElement' && $(this).parent().parent().parent().attr("class") !== 'disabledComponent') {
            inputOK = false;
            //$("#errorMsg_" + this.id).html("This field is mandatory!");
            $("#errorMsg_" + this.id).html(ccfOptions.alert.mandatory_field);
            var error = document.createElement('p');
            //$(error).html(validationProfiles[key].attributes.label + ": mandatory!");
            $(error).html(validationProfiles[key].attributes.label + ccfOptions.alert.mandatory_field_box);
            //$(errorSpace).append(error);
        } else {
            $("#errorMsg_" + this.id).html("");
        }
        if (validationProfiles[key].attributes.ValueScheme === 'date' && this.value !== "") {
            var str = this.value;
            if (!isValidDate(str)) {
                inputOK = false;
                //$("#errorMsg_" + this.id).html("This is not a valid date!");
                $("#errorMsg_" + this.id).html(ccfOptions.alert.no_valid_date);
                var error = document.createElement('p');
                //$(error).html(validationProfiles[key].attributes.label + ": not a valid date!");
                $(error).html(validationProfiles[key].attributes.label + ccfOptions.alert.no_valid_date_box);
                $(errorSpace).append(error);
            }
        }
        if (validationProfiles[key].attributes.ValueScheme === 'int' && this.value !== "") {
            var str = this.value;
            if (!isInteger(str)) {
                inputOK = false;
                $("#errorMsg_" + this.id).html(ccfOptions.alert.int_field);
                var error = document.createElement('p');
                //$(error).html(validationProfiles[key].attributes.label + ccfOptions.alert.int_field_box);
                $(errorSpace).append(error);
            }
        }
    });
}

function isInteger(x) {
    return (Number(x) !== NaN) && (x % 1 === 0);
}

function isValidDate(dateString) {
    var regEx = /^\d{4}-\d{2}-\d{2}$/;
    if (!dateString.match(regEx))
        return false;  // Invalid format
    var d = new Date(dateString);
    if (Number.isNaN(d.getTime()))
        return false; // Invalid date
    return d.toISOString().slice(0, 10) === dateString;
}

function validateTextArea(key) {
    $("[data-validation-profile=" + key + "]").each(function () {
        if (validationProfiles[key].attributes.CardinalityMin === '1' && this.value === "" && $(this).parent().parent().attr("class") !== 'disabledElement' && $(this).parent().parent().parent().attr("class") !== 'disabledComponent') {
            inputOK = false;
            $("#errorMsg_" + this.id).html(ccfOptions.alert.mandatory_field);
            var error = document.createElement('p');
            $(error).html(validationProfiles[key].attributes.label + ccfOptions.alert.mandatory_field_box);
            $(errorSpace).append(error);
        } else {
            $("#errorMsg_" + this.id).html("");
        }
    });
}

function validateSelect(key) {
    $("[data-validation-profile=" + key + "]").each(function () {
        if (validationProfiles[key].attributes.CardinalityMin === '1' && this.selectedIndex === 0 && $(this).parent().parent().attr("class") !== 'disabledElement' && $(this).parent().parent().parent().attr("class") !== 'disabledComponent') {
            inputOK = false;
            $("#errorMsg_" + this.id).html(ccfOptions.alert.mandatory_field);
            var error = document.createElement('p');
            $(error).html(validationProfiles[key].attributes.label + ccfOptions.alert.mandatory_field_box);
            $(errorSpace).append(error);
        } else {
            $("#errorMsg_" + this.id).html("");
        }
    });
}

function getInputType(element) {
    if (element.is("input")) {
        return "input";
    } else {
        if (element.is('textarea')) {
            return "textarea";
        }
        else {
            if (element.is("select")) {
                return "select";
            }
            else {
                return "onbekend";
            }
        }
    }
}





