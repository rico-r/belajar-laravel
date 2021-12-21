<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semua Post</title>

    <link rel="stylesheet" href="{{ asset('css/vendor/bootstrap.min.css') }}">
    <style>
        td:first-child {
            width: 20%;
            word-break: break-word;
        }
        #editDialog #spinner {
            position: absolute;
            right: 2rem;
            top: 0.6875rem;
        }
        .alert, .form-control {margin-top: 1rem}
        .tooltip.error > .tooltip-inner {
            background-color: red;
            color: white;
        }
        .tooltip .tooltip-arrow::before {
            border-bottom-color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mt-5">Semua Post</h1>
        <button class="btn btn-light btn-outline-primary" onclick="showCreatePostDialog()"><i data-cs-icon="plus"></i></button>
        <button class="btn btn-light btn-outline-primary" onclick="reloadData()"><i data-cs-icon="refresh-horizontal"></i></button>
        <br>
        <div id="successAlert" class="alert alert-success" style="display: none;"></div>
        <div id="errorAlert" class="alert alert-danger" style="display: none;"></div>
        <table width="100%" cellpadding="5" class="table-bordered mt-2">
            <thead>
                <tr>
                    <th>Tittle</th>
                    <th>Body</th>
                    <th colspan="2">Action</th>
                </tr>
                <tr id="postTemplate" style="display: none">
                    <td id="tittle">Judul</td>
                    <td id="body">Content</td>
                    <td>
                        <button id="edit" class="btn btn-light btn-outline-primary" onclick="startEditData($(this.parentElement.parentElement))"><i data-cs-icon="edit"></i></button>
                    </td>
                    <td>
                        <button id="delete" class="btn btn-light btn-outline-danger" onclick="confirmDeleteData($(this.parentElement.parentElement))"><i data-cs-icon="bin"></i></button>
                    </td>
                </tr>
                <tr id="loadingSpinner">
                    <td colspan="4" class="text-center" style="height: 300px">
                        <span class="spinner-border text-primary"></span>
                    </td>
                </tr>
                <tr id="loadFailed" style="display: none;">
                    <td colspan="4" class="text-center" style="height: 300px;">
                        <p>Gagal memuat data.</p>
                        <button class="btn btn-light btn-outline-primary" onclick="reloadData()"><i data-cs-icon="refresh-horizontal"></i><br>Muat ulang</button>
                    </td>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <div id="editDialog" class="modal text-dark" role="dialog" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 id="tittle" class="modal-tittle w-100 text-center"></h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form role="form" onsubmit="return false;">
                        <div class="form-group">
                            <input type="text" name="tittle" class="form-control" maxlength="150" placeholder="Tittle" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-custom-class="error" required>
                            <div class="invalid-tooltip"></div>
                        </div>
                        <textarea name="body" rows="10" class="form-control w-100" placeholder="Body" required></textarea>
                        <div class="alert alert-danger mb-0" id="formError" style="display: none;"><ul></ul></div>
                        <button type="submit" class="btn btn-primary position-relative w-100 mt-3" onclick="submitPostData($(this.parentElement))">
                            <span id="submitText"></span>
                            <span id="spinner" class="spinner-border spinner-border-sm" style="display: none;"></span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
        
    <div id="comfirmDelete" class="modal text-dark" role="dialog" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 id="tittle" class="modal-tittle w-100 text-center"></h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-footer flex-nowrap">
                    <button type="submit" class="btn btn-primary w-50"  data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger w-50" onclick="deletePost()">Hapus</button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/vendor/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('js/vendor/bootstrap.bundle.js') }}"></script>
    <script src="{{ asset('js/vendor/csicons.min.js') }}"></script>
    <script>var controller="{{ route('posts.index') }}/";</script>
    @verbatim
    <script>
        var currentTarget;
        var postData;
        cookieStore.get('XSRF-TOKEN').then((result)=>{
            $.ajaxSettings['headers']={'X-XSRF-TOKEN': decodeURIComponent(result.value)};
        }, ()=>{
            // failed to get cookie, cookie is probably disabled
        })

        function reloadData(){
            var table=$("table > tbody > *").remove();
            $("#loadingSpinner").show();
            $("#loadFailed").hide();
            $.ajax({
                url: controller,
                method: "GET",
                dataType: "json",
                success: (data, textStatus, xhr) => {
                    showData(data);
                },
                error: (xhr, textStatus, errorThrown) => {
                    $("#loadFailed").show();
                },
                complete: (xhr, textStatus) => {
                    $("#loadingSpinner").hide();
                }
            });
        }

        function submitPostData(thisForm){
            var dialog = $("#editDialog");
            var test=thisForm.find(":invalid, .is-invalid");
            if(test.length!=0){
                test.addClass("is-invalid");
                test.one("input", (e) => $(e.target).removeClass("is-invalid"));
                test.first().focus();
                return;
            }

            var loading=dialog.find("#spinner");
            var dialogForm = dialog.find("form")[0];
            loading.show();
            loading.parent().attr("disabled", true);
            cleanFormError(dialog);
            var request={
                url: controller + (currentTarget ? currentTarget[0].id : ''),
                method: "POST",
                dataType: "json",
                data: {
                    "tittle": dialogForm.tittle.value,
                    "body": dialogForm.body.value
                }
            };
            if(currentTarget!=null){
                request.data._method = "PUT";
            }
            request.success = (data, textStatus, xhr) => {
                bootstrap.Modal.getOrCreateInstance(dialog[0]).hide();
                if(currentTarget==null){
                    showSucccess("Post barhasil dibuat.");
                    $("table > tbody").append(currentTarget=$("thead > #postTemplate").clone());
                    currentTarget.fadeIn("slow");
                    currentTarget.attr("id", data.id)
                }else{
                    showSucccess("Post barhasil diedit.");
                }
                currentTarget.find("#tittle").text(dialogForm.tittle.value);
                currentTarget.find("#body").text(dialogForm.body.value);
            };
            request.error = (xhr, textStatus, errorThrown) => {
                if(xhr.status==422){
                    var errors=xhr.responseJSON.errors;
                    var firstField = null;
                    for(const fieldName in errors){  
                        var field=$(dialogForm[fieldName]);
                        field.addClass("is-invalid");
                        field.one("input", function(e){
                            $(e.target).removeClass("is-invalid");
                            var tooltip = bootstrap.Tooltip.getOrCreateInstance(e.target);
                            tooltip._config.title = '';
                            tooltip.hide();
                        });
                        var tooltip = bootstrap.Tooltip.getOrCreateInstance(field);
                        tooltip._config.title = errors[fieldName][0];
                        tooltip.show();
                        if(firstField===null) firstField = field;
                    }
                    firstField.focus();
                }else{
                    add("Tidak dapat meng hubungkan ke server.");
                }
            };
            request.complete = (xhr, textStatus) => {
                loading.hide();
                loading.parent().attr("disabled", false);
            }
            $.ajax(request);
        }
        function deletePost(){
            bootstrap.Modal.getOrCreateInstance($("#comfirmDelete")[0]).hide();
            $.ajax({
                "url": controller + currentTarget[0].id,
                "method": "POST",
                "dataType": "json",
                "data": {
                    _method: "DELETE"
                },
                "success": (data, textStatus, xhr) => {
                    currentTarget.remove();
                    showSucccess("Post berhasil dihapus");
                },
                "error": (xhr, textStatus, errorThrown) => {
                    showError("Gagal menghapus post");
                },
                "complete": (xhr, textStatus) => {}
            });
        }

        function showCreatePostDialog(){
            currentTarget=null;
            var dialog = $("#editDialog");
            cleanFormError(dialog);
            dialog.find("#tittle").text("Buat post baru");
            dialog.find("#submitText").text("Buat post");
            dialog.find("[name]").val("");
            bootstrap.Modal.getOrCreateInstance(dialog[0]).show();
            dialog.find(".form-control")[0].focus();
        }

        function startEditData(current){
            currentTarget=current;
            var dialog = $("#editDialog");
            var dialogForm = dialog.find("form")[0];
            cleanFormError(dialog);
            dialog.find("#tittle").text("Edit Post");
            dialog.find("#submitText").text("Edit post");
            dialogForm.tittle.value=current.find("#tittle").text();
            dialogForm.body.value=current.find("#body").text();
            bootstrap.Modal.getOrCreateInstance(dialog[0]).show();
        }

        function cleanFormError(parent){
            $("#formError").hide();
            parent.find(".is-invalid").removeClass("is-invalid");
            var tooltip = bootstrap.Tooltip.getOrCreateInstance(parent.find("[data-bs-toggle='tooltip']")[0]);
            tooltip._config.title = '';
            tooltip.hide();
        }

        function confirmDeleteData(current){
            currentTarget=current;
            var dialog = $("#comfirmDelete");
            dialog.find("#tittle").text("Anda yakin ingin menghapus \""+current.find("#tittle").text()+"\"?");
            bootstrap.Modal.getOrCreateInstance(dialog[0]).show();
        }

        function showData(src){
            var table=$("table > tbody");
            var template=$("#postTemplate")
            for(var i in src){
                var data=src[i];
                var current=template.clone();
                current.show();
                current.find("#tittle").text(data.tittle);
                current.find("#body").text(data.body);
                current.attr("id", data.id);
                table.append(current);
            }
        }

        function showAlert(view, msg){
            if(msg)view.text(msg);
            view.show();
            setTimeout(() => {
                view.hide();
            }, 5000);
        }

        function showSucccess(msg){
            showAlert($("#successAlert"), msg);
        }

        function showError(msg){
            showAlert($("#errorAlert"), msg);
        }

        (function(){
            csicons.replace();
            reloadData();
        })();
    </script>
    @endverbatim
</body>
</html>