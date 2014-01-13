function update_files() {
    ajax_action('getFilesInformation', [], function(msg) {
        var newFiles = [];
        for (var i in msg.files) {
            newFiles.push(getFileString(msg.files[i]) + "<br>");
        }
        var newFilesString = newFiles.join("<br>\n");

        if (newFilesString == "") {
            setStatus("Нет информации ни об одном файле");
        } else {
            setFilesInformation(newFilesString);
        }
    });
}


function getFileString(file) {
    var innerString = "";

    innerString += ['<input type="hidden" id="cur_id" value="', file.ID, '">'].join('');
    innerString += ['id: <input type="text" id="id" value="', file.ID, '">'].join('');
    innerString += ['Name: <input type="text" id="Name" value="', file.Name, '">'].join('');
    innerString += ['Url: <input type="text" id="Url" value="', file.Url, '">'].join('');
    innerString += ['Filename: <input type="text" id="Filename" value="', file.Filename, '">'].join('');
    innerString += ['Last_update: <input type="text" id="Last_update" value="', file.Last_update, '">'].join('');
    innerString += ['MinRange: <input type="text" id="MinRange" value="', file.MinRange, '">'].join('');
    innerString += ['MaxRange: <input type="text" id="MaxRange" value="', file.MaxRange, '">'].join('');

    innerString += ['<input type="button" onclick="updFile(', file.ID, ');" value="Обновить">'].join('');
    innerString += ['<input type="button" onclick="delFile(', file.ID, ');" value="Удалить">'].join('');

    return ['<div id="row', file.ID, '">', innerString, '</div>'].join("");
}

function setFilesInformation(text) {
    $("#files_container").html(text);
}

function delFile(id) {
    var params = [];
    params['id'] = id;

    ajax_action('delFilesInformation', params, function(msg) {
        update_files();
        setStatus(['Файл id=', id, ' удален'].join(''));
    });
}

function updFile(id) {
    var params = getFileInfoById(id);

    ajax_action('updFilesInformation', params, function(msg) {
        update_files();
        setStatus(['Файл id=', id, ' обновлен'].join(''));
    });
}

function newFile() {
    var params = getFileInfoById("new");

    ajax_action('newFilesInformation', params, function(msg) {
        update_files();
        setStatus('Создан новый файл');
    });
}

function getFileInfoById(id) {
    var file_id = "";
    if (id == "new") {
        file_id = "#new_file";
    } else {
        file_id = ["#", 'row', id].join("");
    }

    var params = [];


    params["id"] = $(file_id + " > #id").val();
    params["Name"] = $(file_id + " > #Name").val();
    params["Url"] = $(file_id + " > #Url").val();
    params["Filename"] = $(file_id + " > #Filename").val();
    params["Last_update"] = $(file_id + " > #Last_update").val();
    params["MinRange"] = $(file_id + " > #MinRange").val();
    params["MaxRange"] = $(file_id + " > #MaxRange").val();

    if (id != "new") {
        params["curr_id"] = $(file_id + " > #cur_id").val();
    }

    return params;
}

function setStatus(status) {
    if (status == "") {
        status = "&nbsp;";
    }
    $("#status").html(status);
}

function ajax_action(subfunc, params, processing) {
    setStatus("Подождите..");
    var requestParams = {
        func: 'FilesInformation',
        subfunc: subfunc
    };

    for (var x in params) {
        requestParams[x] = params[x];
    }

    $.ajax({
        type: "POST",
        dataType: "json",
        url: "action.php",
        data: requestParams,
        async: true,
        success: function(msg){
            if (msg.result != "success") {
                setStatus(msg.errorMessage);
                return;
            }

            setStatus('');
            processing(msg);
        },
        error: function(jqXHR, textStatus, errorThrown ) {
            setStatus("Возникла ошибка. Обратитесь, пожалуйста, к разработчику.");
        }
    });
}