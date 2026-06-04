// Summernote untuk field #desc (jika ada)
if ($('#desc').length && typeof $.fn.summernote !== 'undefined') {
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
    if (noteModal) {
        noteModal.style.zIndex = 9999;
        var noteCheckbox = noteModal.querySelector('.checkbox');
        if (noteCheckbox) noteCheckbox.style.display = 'none';
        var noteContent = noteModal.querySelector('.note-modal-content');
        if (noteContent) noteContent.style.padding = '3px';
    }
}

// Bootstrap FileInput (hanya jika plugin tersedia DAN ada elemen .file-drag-drop)
if (typeof $.fn.fileinput !== 'undefined' && $(".file-drag-drop").length) {
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
}

// Select2 (jika plugin tersedia)
if (typeof $.fn.select2 !== 'undefined') {
    $('.select2').each(function () {
        let dropdownParent = $(this).closest('form');
        $(this).select2({
            placeholder: "Silahkan Pilih",
            dropdownParent: dropdownParent
        });
    });
}