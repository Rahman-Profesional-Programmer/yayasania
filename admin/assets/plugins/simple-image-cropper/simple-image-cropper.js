(function () {
    function clamp(value, min, max) {
        return Math.min(Math.max(value, min), max);
    }

    function dataUrlToBlob(dataUrl) {
        var parts = dataUrl.split(',');
        var match = parts[0].match(/data:(.*?);base64/);
        var mime = match ? match[1] : 'image/jpeg';
        var binary = atob(parts[1]);
        var length = binary.length;
        var bytes = new Uint8Array(length);

        for (var index = 0; index < length; index += 1) {
            bytes[index] = binary.charCodeAt(index);
        }

        return new Blob([bytes], { type: mime });
    }

    function createCropperElements() {
        var overlay = document.createElement('div');
        overlay.className = 'simple-cropper-modal';
        overlay.innerHTML = [
            '<div class="simple-cropper-dialog">',
            '  <div class="simple-cropper-header">',
            '    <div>',
            '      <h5 style="margin:0;">Crop Gambar</h5>',
            '      <small style="color:#64748b;">Geser gambar dan atur zoom untuk menyesuaikan area tampilan artikel.</small>',
            '    </div>',
            '    <button type="button" class="btn btn-outline-secondary btn-sm" data-cropper-close>Tutup</button>',
            '  </div>',
            '  <div class="simple-cropper-body">',
            '    <div class="simple-cropper-stage" data-cropper-stage>',
            '      <img class="simple-cropper-image" data-cropper-image alt="Crop preview">',
            '      <div class="simple-cropper-grid"></div>',
            '    </div>',
            '    <div class="simple-cropper-controls">',
            '      <label class="form-label" for="simpleCropperZoom">Zoom</label>',
            '      <input id="simpleCropperZoom" class="simple-cropper-range" data-cropper-zoom type="range" min="1" max="3" step="0.01" value="1">',
            '    </div>',
            '  </div>',
            '  <div class="simple-cropper-footer">',
            '    <span style="color:#64748b;font-size:13px;">Rasio crop: 16:9</span>',
            '    <div class="d-flex gap-2">',
            '      <button type="button" class="btn btn-light" data-cropper-cancel>Batal</button>',
            '      <button type="button" class="btn btn-primary" data-cropper-apply>Gunakan Gambar</button>',
            '    </div>',
            '  </div>',
            '</div>'
        ].join('');
        document.body.appendChild(overlay);
        return overlay;
    }

    var overlay = null;

    function openCropper(options) {
        overlay = overlay || createCropperElements();

        var stage = overlay.querySelector('[data-cropper-stage]');
        var image = overlay.querySelector('[data-cropper-image]');
        var zoomInput = overlay.querySelector('[data-cropper-zoom]');
        var closeButtons = overlay.querySelectorAll('[data-cropper-close], [data-cropper-cancel]');
        var applyButton = overlay.querySelector('[data-cropper-apply]');
        var aspectRatio = options.aspectRatio || (16 / 9);
        var outputWidth = options.outputWidth || 1280;
        var outputHeight = Math.round(outputWidth / aspectRatio);
        var dragging = false;
        var dragStartX = 0;
        var dragStartY = 0;
        var currentX = 0;
        var currentY = 0;
        var startX = 0;
        var startY = 0;
        var scale = 1;
        var minScale = 1;
        var naturalWidth = 0;
        var naturalHeight = 0;
        var objectUrl = URL.createObjectURL(options.file);

        function destroyObjectUrl() {
            if (objectUrl) {
                URL.revokeObjectURL(objectUrl);
                objectUrl = null;
            }
        }

        function getStageRect() {
            return stage.getBoundingClientRect();
        }

        function constrainPosition() {
            var rect = getStageRect();
            var renderedWidth = naturalWidth * scale;
            var renderedHeight = naturalHeight * scale;
            var minX = rect.width - renderedWidth;
            var minY = rect.height - renderedHeight;
            currentX = clamp(currentX, Math.min(0, minX), 0);
            currentY = clamp(currentY, Math.min(0, minY), 0);
        }

        function render() {
            image.style.transform = 'translate(' + currentX + 'px, ' + currentY + 'px) scale(' + scale + ')';
        }

        function resetView() {
            var rect = getStageRect();
            minScale = Math.max(rect.width / naturalWidth, rect.height / naturalHeight);
            scale = Math.max(minScale, 1);
            zoomInput.min = String(minScale);
            zoomInput.max = String(Math.max(minScale + 2, minScale * 3));
            zoomInput.value = String(scale);
            currentX = (rect.width - naturalWidth * scale) / 2;
            currentY = (rect.height - naturalHeight * scale) / 2;
            constrainPosition();
            render();
        }

        function close() {
            overlay.classList.remove('is-open');
            stage.classList.remove('is-dragging');
            destroyObjectUrl();
            image.removeAttribute('src');
            applyButton.onclick = null;
            closeButtons.forEach(function (button) {
                button.onclick = null;
            });
            stage.onpointerdown = null;
            stage.onpointermove = null;
            stage.onpointerup = null;
            stage.onpointerleave = null;
            zoomInput.oninput = null;
            window.onresize = null;
        }

        closeButtons.forEach(function (button) {
            button.onclick = close;
        });

        image.onload = function () {
            naturalWidth = image.naturalWidth;
            naturalHeight = image.naturalHeight;
            resetView();
        };

        stage.onpointerdown = function (event) {
            dragging = true;
            stage.classList.add('is-dragging');
            dragStartX = event.clientX;
            dragStartY = event.clientY;
            startX = currentX;
            startY = currentY;
            stage.setPointerCapture(event.pointerId);
        };

        stage.onpointermove = function (event) {
            if (!dragging) {
                return;
            }
            currentX = startX + (event.clientX - dragStartX);
            currentY = startY + (event.clientY - dragStartY);
            constrainPosition();
            render();
        };

        stage.onpointerup = function () {
            dragging = false;
            stage.classList.remove('is-dragging');
        };

        stage.onpointerleave = function () {
            dragging = false;
            stage.classList.remove('is-dragging');
        };

        zoomInput.oninput = function () {
            var rect = getStageRect();
            var previousScale = scale;
            var nextScale = Math.max(Number(zoomInput.value), minScale);
            var centerX = rect.width / 2;
            var centerY = rect.height / 2;
            var offsetX = (centerX - currentX) / previousScale;
            var offsetY = (centerY - currentY) / previousScale;

            scale = nextScale;
            currentX = centerX - offsetX * scale;
            currentY = centerY - offsetY * scale;
            constrainPosition();
            render();
        };

        applyButton.onclick = function () {
            var rect = getStageRect();
            var canvas = document.createElement('canvas');
            var context = canvas.getContext('2d');
            canvas.width = outputWidth;
            canvas.height = outputHeight;

            var cropX = -currentX / scale;
            var cropY = -currentY / scale;
            var cropWidth = rect.width / scale;
            var cropHeight = rect.height / scale;

            context.drawImage(image, cropX, cropY, cropWidth, cropHeight, 0, 0, outputWidth, outputHeight);
            var dataUrl = canvas.toDataURL('image/jpeg', 0.92);
            options.onCrop({
                dataUrl: dataUrl,
                blob: dataUrlToBlob(dataUrl),
                width: outputWidth,
                height: outputHeight,
                fileName: options.file.name || 'cropped-image.jpg'
            });
            close();
        };

        window.onresize = function () {
            if (overlay.classList.contains('is-open')) {
                resetView();
            }
        };

        image.src = objectUrl;
        overlay.classList.add('is-open');
    }

    window.SimpleImageCropper = {
        open: openCropper
    };
})();
