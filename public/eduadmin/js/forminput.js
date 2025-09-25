$('#desc').summernote({
        tabsize: 2,
        height: 250,
        toolbar: [
            "fontsize",
            "paragraph",
            "table",
            "insert",
            "codeview",
            "link",
        ],
        fontSizes: ['8', '9', '10', '11', '12', '14', '18', '24', '36'],
    });
    var noteModal = document.querySelector('.note-modal');
    noteModal.style.zIndex = 9999;
    noteModal.querySelector('.checkbox').style.display = 'none';
    noteModal.querySelector('.note-modal-content').style.padding = '3px';

    $(".file-drag-drop").fileinput({
        theme: 'fa',
        uploadUrl: "/#",
        showUpload: false,
        showRemove: false,
        showCancel: false,
        showClose: false,
        allowedFileExtensions: ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx', 'xls', 'xlsx'],
        overwriteInitial: true,
        maxFileSize: 2048,
        maxFilesNum: 10,
        slugCallback: function (filename) {
            return filename.replace('(', '_').replace(']', '_');
        },
        initialPreviewAsData: true,
    });

    $('.select2').each(function () {
        let dropdownParent = $(this).closest('form');
        $(this).select2({
            placeholder: "Silahkan Pilih",
            dropdownParent: dropdownParent
        });
    });
    