a17cms.Behaviors.uploader = function(element){
    var debug = true;
    var domElement = element.get(0);
    var datas = element.data();

    function onCompleteCallback(id, name, responseJSON, xhr) {
        if (responseJSON.success) {
            this.uploadSessionIds.push(responseJSON.id);
        }
    }

    function onAllCompleteCallback(succeeded, failed) {
        // reset folder name for next upload session
        this.unique_folder_name = null;
        if (failed.length == 0) {
            location.href = datas.uploadCompleteEndpoint + this.uploadSessionIds;
            this.uploadSessionIds = [];
        }
    }

    function onSubmitCallback(id, name) {
        // each upload session will add upload files with original filenames in a folder named using a uuid
        this.unique_folder_name = this.unique_folder_name || qq.getUniqueId();
        this.uploadSessionIds = [];
        this.setParams({ unique_folder_name: this.unique_folder_name }, id);

        // here we determine the image dimensions and add it to params sent on upload success
        var imageUrl = URL.createObjectURL(this.getFile(id));
        var img = new Image;
        img.onload = function() {
            uploader.setParams({ width: img.width, height: img.height }, id);
        };
        img.src = imageUrl;
    }

    function initForLocal() {
        var uploader = new qq.FineUploader({
            debug: debug,
            element: domElement,
            request: {
                endpoint: datas.uploadEndpoint,
                customHeaders: {
                    'X-CSRF-TOKEN': datas.uploadCsrfToken
                }
            },
            callbacks: {
                onSubmit: onSubmitCallback,
                onAllComplete: onAllCompleteCallback,
                onComplete: onCompleteCallback,
            },
            validation: {
                sizeLimit: datas.uploadFilesizeLimit * 1048576 // mb to bytes
            },
        });
    }

    function initForS3() {
        var uploader = new qq.s3.FineUploader({
            debug: debug,
            element: domElement,
            objectProperties: {
                key: function (id) {
                    return this.unique_folder_name + '/' + this.getName(id).noAccents(true).toFileName(true).toLowerCase();
                },
                region: datas.uploadEndpointRegion,
                acl: datas.uploadAcl,
            },
            request: {
                endpoint: datas.uploadEndpoint,
                accessKey: datas.uploadAccessKey
            },
            signature: {
                endpoint: datas.uploadSignatureEndpoint,
                version: 4,
                customHeaders: {
                    'X-CSRF-TOKEN': datas.uploadCsrfToken
                }
            },
            uploadSuccess: {
                endpoint: datas.uploadSuccessEndpoint,
                customHeaders: {
                    'X-CSRF-TOKEN': datas.uploadCsrfToken
                }
            },
            callbacks: {
                onSubmit: onSubmitCallback,
                onAllComplete: onAllCompleteCallback,
                onComplete: onCompleteCallback,
            },
        });
    }

    datas.uploadEndpointType === 'local' ? initForLocal() : initForS3();

};
