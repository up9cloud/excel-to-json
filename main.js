var ajax = {};
ajax.x = function () {
    if (typeof XMLHttpRequest !== 'undefined') {
        return new XMLHttpRequest();
    }
    var versions = [
        "MSXML2.XmlHttp.6.0",
        "MSXML2.XmlHttp.5.0",
        "MSXML2.XmlHttp.4.0",
        "MSXML2.XmlHttp.3.0",
        "MSXML2.XmlHttp.2.0",
        "Microsoft.XmlHttp"
    ];
    var xhr;
    for (var i = 0; i < versions.length; i++) {
        try {
            xhr = new ActiveXObject(versions[i]);
            break;
        } catch (e) {}
    }
    return xhr;
};
ajax.send = function (url, successCallback, errorCallback, alwaysCallback, method, data, header, sync) {
    var x = ajax.x();
    x.open(method, url, true);
    x.onreadystatechange = function () {
        if (x.readyState != 4) return;
        if (x.status != 200 && x.status != 304) {
            if (errorCallback)
                errorCallback(x.responseText, x.status);
        } else {
            if (successCallback) {
                successCallback(x.responseText, x.status);
            }
        }
        if(alwaysCallback){
            alwaysCallback(x.responseText, x.status);
        }
    };
    if (method === 'GET') {
        x.send();
    } else {
        if(header===false){

        }else{
            x.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        }
        x.send(data);
    }
};
var lock_buttons=function(){
	var btns=document.getElementsByTagName('button');
	for (var i = 0; i < btns.length; i++) {
		btns[i].disabled=true;
	}
};
var unlock_buttons=function(){
	var btns=document.getElementsByTagName('button');
	for (var i = 0; i < btns.length; i++) {
		btns[i].disabled=false;
	}
}
var root = document.location.href.substring(0, document.location.href.lastIndexOf('/'));
var buildParams = function (arr) {
    var r = [];
    arr.forEach(function (val) {
        r.push('argv[]=' + val);
    });
    return r.join('&');
};
var execute = function (params, successCallback, failureCallback) {
    var file='do.php';
    params.unshift(file);
    lock_buttons();
    ajax.send(root + '/' + file, function (res, code) {
        if(successCallback){
            successCallback(res);
        }else{
            document.getElementById('response').innerHTML = res;
        }
    }, function (err, code) {
        if(failureCallback){
            failureCallback(res);
        }else{
            document.getElementById('response').innerHTML = err;
        }
    }, function (res, code){
        unlock_buttons();
    }, 'POST', buildParams(
        params
    ));
};
var do_project_file = function (project, file) {
    execute([project, file]);
};
var current_project='';
var rebuild_project_buttons=function(projects){
    var p=document.getElementById('projects');
    p.innerHTML='';
    projects.forEach(function(project_name){
        var btn=document.getElementById('refresh_project').cloneNode(true);
        btn.id='';
        btn.querySelector('span').innerHTML=project_name;
        btn.classList.remove();
        btn.classList.add('btn');
        btn.classList.remove('btn-xs');
        btn.classList.add('btn-warning');
        var active_class='active';
        // 改變顏色
        if(current_project===project_name){
            btn.classList.add(active_class);
        }
        btn.addEventListener('click', function(){
            current_project=project_name;
            getProject(project_name);
            // 改變顏色
            var list=p.querySelectorAll('button');
            for (var i = 0; i < list.length; i++) {
                list[i].classList.remove(active_class);
            };
            btn.classList.add(active_class);
        });
        p.appendChild(btn);
    });
};
var rebuild_project_file_buttons=function(files){
    var p=document.getElementById('files');
    p.innerHTML='';
    // add do all files button.
    var btn=document.getElementById('refresh_project').cloneNode(true);
    btn.id='';
    btn.querySelector('span').innerHTML='do all';
    btn.classList.remove();
    btn.classList.add('btn');
    btn.classList.add('btn-xs');
    btn.classList.add('btn-warning');
    btn.addEventListener('click', function(){
        execute([current_project]);
    });
    p.appendChild(btn);
    // all files.
    files.forEach(function(file){
        var btn=document.getElementById('refresh_project').cloneNode(true);
        btn.id='';
        btn.querySelector('span').innerHTML=file;
        btn.classList.remove();
        btn.classList.add('btn');
        btn.classList.add('btn-xs');
        btn.classList.add('btn-success');
        btn.addEventListener('click', function(){
            execute([current_project, file]);
        });
        p.appendChild(btn);
    });
};
var getProject=function(project){
    if(project){
        execute(['--project', project], function(res){
            rebuild_project_file_buttons(JSON.parse(res));
        });
    }else{
        var p=document.getElementById('projects');
        p.innerHTML='<i class="fa fa-spinner fa-4x fa-spin"></i>';
        execute(['--projects'], function(res){
            rebuild_project_buttons(JSON.parse(res));
        });
    }
};
document.getElementById('do_all').addEventListener('click', function(){
    execute(['--all']);
});
document.getElementById('refresh_project').addEventListener('click', function(){
    getProject();
});
document.getElementById('help').addEventListener('click', function(){
    execute(['--help']);
});
// upload file events.
// http://blog.teamtreehouse.com/uploading-files-ajax
document.getElementById('choose_file').addEventListener('click', function(){
    document.getElementById('input_file').click();
});
document.getElementById('input_file').addEventListener('change', function(){
    document.getElementById('upload_file').innerHTML=this.value.split(/[\\|/]/).pop();
});
document.getElementById('input_file').addEventListener('load', function(){
    document.getElementById('upload_file').innerHTML='';
});
document.getElementById('upload').addEventListener('click', function(){
    var res_node = document.getElementById('response');
    //check project clicked.
    if(current_project===''){
        res_node.innerHTML='未選擇 project ！';
        return;
    }
    var btn=document.getElementById('upload')
    var lock=function(){
        btn.textContent='上傳中...';
        lock_buttons();
    };
    var unlock=function(){
        btn.textContent='upload';
        unlock_buttons();
    }
    var input = document.getElementById('input_file');
    var files = input.files;
    if(files.length<=0){
        res_node.innerHTML='未選擇檔案！';
        return;
    }
    var formData = new FormData();
    formData.append('project', current_project);
    // Loop through each of the selected files.
    for (var i = 0; i < files.length; i++) {
        var file = files[i];
        // Check the file type.
        if (!file.name.match('.xls')) {
            res_node.innerHTML='file ext not allow！';
            return;
        }
        // Add the file to the request.
        // Files
        formData.append('file', file, file.name);
        // // Blobs
        // formData.append(name, blob, filename);
        // // Strings
        // formData.append(name, value);
    }
    lock();
    ajax.send(root + '/' + 'upload.php', function (res, code) {
        document.getElementById('response').innerHTML = res;
    }, function (err, code) {
        document.getElementById('response').innerHTML = res;
    }, function (res, code){
        unlock();
    }, 'POST', formData, false);
});
getProject();

