@section('extra_css')
    <link href="/assets/admin/vendor/fine-uploader/fine-uploader-gallery.min.css" rel="stylesheet" type="text/css"/>
    <style>
        .qq-gallery .qq-upload-button {
            background: #333333;
            border-radius: none;
            border: none;
            box-shadow: none;
        }

        .qq-gallery.qq-uploader {
            min-height: 150px;
            max-height: unset;
            border-radius: 0px;
            border: 1px dashed #d9d9d9;
            background-color: #f3f3f3;
        }

       .qq-gallery .qq-progress-bar {
            background: #3278b8;
            border-radius: 0;
        }

        .qq-gallery .qq-alert-dialog-selector {
            top: 150px;
            left: 42px;
        }

        .qq-gallery.qq-uploader dialog .qq-dialog-buttons {
            text-align: left;
        }
    </style>
@stop

@section('extra_js')
    <script src="/assets/admin/vendor/fine-uploader/all.fine-uploader.min.js"></script>
    <script src="/assets/admin/vendor/stringops/stringops.js"></script>
    <script src="/assets/admin/behaviors/uploader.js"></script>
@stop

<!-- The element where Fine Uploader will exist. -->
<div id="uploader"
    data-behavior="uploader"
    data-upload-endpoint-type="{{ $endpointType }}"
    data-upload-endpoint="{{ $endpoint }}"
    data-upload-access-key="{{ $accessKey }}"
    data-upload-success-endpoint="{{ $successEndpoint }}"
    data-upload-complete-endpoint="{{ $completeEndpoint }}"
    data-upload-signature-endpoint="{{ $signatureEndpoint }}"
    data-upload-endpoint-region="{{ $endpointRegion }}"
    data-upload-csrf-token="{{ $csrfToken }}">
</div>

<!-- Fine Uploader template -->
<script type="text/template" id="qq-template">
    <div class="qq-uploader-selector qq-uploader qq-gallery" qq-drop-area-text="Drop new files here">
        {{-- <div class="qq-total-progress-bar-container-selector qq-total-progress-bar-container">
            <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-total-progress-bar-selector qq-progress-bar qq-total-progress-bar"></div>
        </div> --}}
        <div class="qq-upload-drop-area-selector qq-upload-drop-area" qq-hide-dropzone>
            <span class="qq-upload-drop-area-text-selector"></span>
        </div>
        <div class="qq-upload-button-selector qq-upload-button">
            <div>Browse...</div>
        </div>
        <span class="qq-drop-processing-selector qq-drop-processing">
            <span>Processing dropped files...</span>
            <span class="qq-drop-processing-spinner-selector qq-drop-processing-spinner"></span>
        </span>
        <ul class="qq-upload-list-selector qq-upload-list" role="region" aria-live="polite" aria-relevant="additions removals">
            <li>
                <span role="status" class="qq-upload-status-text-selector qq-upload-status-text"></span>
                <div class="qq-progress-bar-container-selector qq-progress-bar-container">
                    <div role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="qq-progress-bar-selector qq-progress-bar"></div>
                </div>
                <span class="qq-upload-spinner-selector qq-upload-spinner"></span>
                <div class="qq-thumbnail-wrapper">
                    <a class="preview-link" target="_blank" style="display: block; height: 100%; width: 100%;">
                        <img class="qq-thumbnail-selector" qq-max-size="120" qq-server-scale>
                    </a>
                </div>
                <button type="button" class="qq-upload-cancel-selector qq-upload-cancel">X</button>
                <button type="button" class="qq-upload-retry-selector qq-upload-retry">
                    <span class="qq-btn qq-retry-icon" aria-label="Retry"></span>
                    Retry
                </button>

                <div class="qq-file-info">
                    <div class="qq-file-name">
                        <span class="qq-upload-file-selector qq-upload-file"></span>
                        <span class="qq-edit-filename-icon-selector qq-btn qq-edit-filename-icon" aria-label="Edit filename"></span>
                    </div>
                    <input class="qq-edit-filename-selector qq-edit-filename" tabindex="0" type="text">
                    <span class="qq-upload-size-selector qq-upload-size"></span>
                    <button type="button" class="qq-btn qq-upload-delete-selector qq-upload-delete">
                        <span class="qq-btn qq-delete-icon" aria-label="Delete"></span>
                    </button>
                    <button type="button" class="qq-btn qq-upload-pause-selector qq-upload-pause">
                        <span class="qq-btn qq-pause-icon" aria-label="Pause"></span>
                    </button>
                    <button type="button" class="qq-btn qq-upload-continue-selector qq-upload-continue">
                        <span class="qq-btn qq-continue-icon" aria-label="Continue"></span>
                    </button>
                </div>
            </li>
        </ul>

        <dialog class="qq-alert-dialog-selector">
            <div class="qq-dialog-message-selector"></div>
            <div class="qq-dialog-buttons">
                <button type="button" class="qq-cancel-button-selector">Cancel</button>
            </div>
        </dialog>

        <dialog class="qq-confirm-dialog-selector">
            <div class="qq-dialog-message-selector"></div>
            <div class="qq-dialog-buttons">
                <button type="button" class="qq-cancel-button-selector">No</button>
                <button type="button" class="qq-ok-button-selector">Yes</button>
            </div>
        </dialog>

        <dialog class="qq-prompt-dialog-selector">
            <div class="qq-dialog-message-selector"></div>
            <input type="text">
            <div class="qq-dialog-buttons">
                <button type="button" class="qq-cancel-button-selector">Cancel</button>
                <button type="button" class="qq-ok-button-selector">Ok</button>
            </div>
        </dialog>
    </div>
</script>
