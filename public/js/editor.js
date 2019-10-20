

InlineEditor
.create( document.querySelector( '#editor-title' ),{
    image: {
        resizeUnit: 'px'
    },
    simpleUpload: {
        uploadUrl: 'http://localhost:8000/upload-picture',
        headers: {
            'X-CSRF-TOKEN': 'CSFR-Token',
            Authorization: 'Bearer <JSON Web Token>'
        }
    }
});

InlineEditor
.create( document.querySelector( '#editor-intro' ),{
    image: {
        resizeUnit: 'px'
    },
    simpleUpload: {
        uploadUrl: 'http://localhost:8000/upload-picture',
        headers: {
            'X-CSRF-TOKEN': 'CSFR-Token',
            Authorization: 'Bearer <JSON Web Token>'
        }
    }
});

InlineEditor
.create( document.querySelector( '#editor-content' ),{
    image: {
        resizeUnit: 'px'
    },
    simpleUpload: {
        uploadUrl: 'http://localhost:8000/upload-picture',
        headers: {
            'X-CSRF-TOKEN': 'CSFR-Token',
            Authorization: 'Bearer <JSON Web Token>'
        }
    }
});

