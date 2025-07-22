<div class="modal fade" id="importExcelModal" tabindex="-1" aria-labelledby="importExcelModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-content-enhanced">
            <div class="modal-header modal-header-enhanced">
                <h5 class="modal-title" id="importExcelModalLabel">
                    <i class="ti ti-file-upload me-2"></i>{{ __('Import Excel File') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body modal-body-enhanced">
                <x-form id="importExcelForm" :action="$importRoute ?? route('admin.product.import')" method="post"
                        enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="excelFile" class="form-label form-label-enhanced">
                            <i class="ti ti-file-excel me-2 text-success"></i>{{ __('Excel File') }}
                        </label>

                        <div class="file-input-wrapper">
                            <div class="form-control-file-enhanced" id="fileDropZone">
                                <input type="file" class="actual-file-input" id="excelFile" name="excelFile"
                                       accept=".xlsx,.xls,.csv" required>

                                <div class="file-input-icon">
                                    <i class="ti ti-cloud-upload"></i>
                                </div>

                                <div class="file-input-text" id="fileInputText">
                                    <strong>Nhấp để chọn file</strong> hoặc kéo thả file vào đây<br>
                                    <small>Chỉ chấp nhận file .xlsx, .xls, .csv</small>
                                </div>
                            </div>

                            <div class="file-info" id="fileInfo">
                                <i class="ti ti-check me-1"></i>
                                <span id="fileDetails"></span>
                            </div>
                        </div>
                    </div>
                </x-form>
            </div>
            <div class="modal-footer modal-footer-enhanced">
                <button type="button" class="btn btn-modal-close" data-bs-dismiss="modal">
                    <i class="ti ti-x me-1"></i>{{ __('Đóng') }}
                </button>
                <button type="submit" form="importExcelForm" class="btn btn-modal-import" id="importBtn">
                    <i class="ti ti-upload me-1"></i>{{ __('Import') }}
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize modal elements
        function initializeModalElements() {
            const fileInput = document.getElementById('excelFile');
            const fileDropZone = document.getElementById('fileDropZone');
            const fileInputText = document.getElementById('fileInputText');
            const fileInfo = document.getElementById('fileInfo');
            const fileDetails = document.getElementById('fileDetails');
            const importBtn = document.getElementById('importBtn');
            const importForm = document.getElementById('importExcelForm');
            const modal = document.getElementById('importExcelModal');

            // Check if all elements exist
            if (!fileInput || !fileDropZone || !fileInputText || !fileInfo || !fileDetails || !importBtn || !importForm || !modal) {
                console.warn('Some modal elements not found');
                return null;
            }

            return {
                fileInput,
                fileDropZone,
                fileInputText,
                fileInfo,
                fileDetails,
                importBtn,
                importForm,
                modal
            };
        }

        // Reset modal to initial state
        function resetModalState(elements) {
            if (!elements) return;

            const { fileInput, fileDropZone, fileInputText, fileInfo, importBtn } = elements;

            // Reset file input
            fileInput.value = '';

            // Reset UI state
            fileDropZone.classList.remove('file-selected');
            fileDropZone.style.borderColor = '#dee2e6';
            fileDropZone.style.backgroundColor = 'white';

            fileInputText.classList.remove('has-file');
            fileInputText.innerHTML = `
            <strong>Nhấp để chọn file</strong> hoặc kéo thả file vào đây<br>
            <small>Chỉ chấp nhận file .xlsx, .xls, .csv</small>
        `;

            // Hide file info
            fileInfo.classList.remove('show');

            // Reset button state
            importBtn.disabled = true;
            importBtn.classList.remove('btn-loading');
            importBtn.innerHTML = '<i class="ti ti-upload me-1"></i>Import';

            // Remove any validation error classes
            fileDropZone.classList.remove('is-invalid');

            // Remove any existing error messages
            const existingError = fileDropZone.parentNode.querySelector('.invalid-feedback');
            if (existingError) {
                existingError.remove();
            }
        }

        // Handle file selection
        function handleFileSelect(file, elements) {
            if (!elements) return;

            const { fileDropZone, fileInputText, fileInfo, fileDetails, importBtn } = elements;

            if (file) {
                // Validate file type
                const allowedTypes = ['.xlsx', '.xls', '.csv'];
                const fileExtension = '.' + file.name.split('.').pop().toLowerCase();

                if (!allowedTypes.includes(fileExtension)) {
                    showFileError('Vui lòng chọn file có định dạng .xlsx, .xls hoặc .csv', elements);
                    return;
                }

                // Validate file size (max 10MB)
                const maxSize = 10 * 1024 * 1024; // 10MB
                if (file.size > maxSize) {
                    showFileError('File quá lớn. Vui lòng chọn file nhỏ hơn 10MB', elements);
                    return;
                }

                const fileSize = (file.size / 1024 / 1024).toFixed(2);
                const fileName = file.name;

                // Update UI - file selected
                fileDropZone.classList.add('file-selected');
                fileDropZone.classList.remove('is-invalid');
                fileInputText.classList.add('has-file');
                fileInputText.innerHTML = `
                <strong>Đã chọn file:</strong><br>
                <small>${fileName}</small>
            `;

                // Show file info
                fileDetails.textContent = `${fileName} (${fileSize} MB)`;
                fileInfo.classList.add('show');

                // Enable import button
                importBtn.disabled = false;

                // Remove any existing error messages
                const existingError = fileDropZone.parentNode.querySelector('.invalid-feedback');
                if (existingError) {
                    existingError.remove();
                }
            } else {
                resetModalState(elements);
            }
        }

        // Show file error
        function showFileError(message, elements) {
            if (!elements) return;

            const { fileInput, fileDropZone } = elements;

            // Reset file input
            fileInput.value = '';

            // Add error styling
            fileDropZone.classList.add('is-invalid');
            fileDropZone.classList.remove('file-selected');

            // Remove existing error message
            const existingError = fileDropZone.parentNode.querySelector('.invalid-feedback');
            if (existingError) {
                existingError.remove();
            }

            // Add error message
            const errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback d-block';
            errorDiv.textContent = message;
            fileDropZone.parentNode.appendChild(errorDiv);

            resetModalState(elements);
        }

        // Setup event listeners
        function setupEventListeners(elements) {
            if (!elements) return;

            const { fileInput, fileDropZone, importForm, modal } = elements;

            // Remove existing listeners to prevent duplicates
            const newFileInput = fileInput.cloneNode(true);
            fileInput.parentNode.replaceChild(newFileInput, fileInput);
            elements.fileInput = newFileInput;

            // File input change handler
            elements.fileInput.addEventListener('change', function(e) {
                handleFileSelect(e.target.files[0], elements);
            });

            // Drag and drop handlers
            fileDropZone.addEventListener('dragover', function(e) {
                e.preventDefault();
                e.stopPropagation();
                fileDropZone.style.borderColor = '#667eea';
                fileDropZone.style.backgroundColor = '#f8f9ff';
            });

            fileDropZone.addEventListener('dragleave', function(e) {
                e.preventDefault();
                e.stopPropagation();
                fileDropZone.style.borderColor = '#dee2e6';
                fileDropZone.style.backgroundColor = 'white';
            });

            fileDropZone.addEventListener('drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                fileDropZone.style.borderColor = '#dee2e6';
                fileDropZone.style.backgroundColor = 'white';

                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    // Manually set files to input (for form submission)
                    const dt = new DataTransfer();
                    dt.items.add(files[0]);
                    elements.fileInput.files = dt.files;
                    handleFileSelect(files[0], elements);
                }
            });

            // Form submit handler
            importForm.addEventListener('submit', function(e) {
                const { importBtn } = elements;

                // Check if file is selected
                if (!elements.fileInput.files || elements.fileInput.files.length === 0) {
                    e.preventDefault();
                    showFileError('Vui lòng chọn file để import', elements);
                    return false;
                }

                const originalText = importBtn.innerHTML;

                // Add loading state
                importBtn.disabled = true;
                importBtn.classList.add('btn-loading');
                importBtn.innerHTML = '<span style="opacity: 0;">Đang import...</span>';

                // Reset loading state after 10 seconds (fallback)
                setTimeout(() => {
                    importBtn.disabled = false;
                    importBtn.classList.remove('btn-loading');
                    importBtn.innerHTML = originalText;
                }, 10000);
            });

            // Reset modal when it's shown
            modal.addEventListener('show.bs.modal', function() {
                resetModalState(elements);
            });

            // Reset modal when closed
            modal.addEventListener('hidden.bs.modal', function() {
                resetModalState(elements);
            });
        }

        // Initialize everything
        function initializeModal() {
            const elements = initializeModalElements();
            if (elements) {
                setupEventListeners(elements);
                resetModalState(elements);
            }
        }

        // Initialize on DOM ready
        initializeModal();

        // Re-initialize if modal is dynamically added later
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList') {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1 && (node.id === 'importExcelModal' || node.querySelector('#importExcelModal'))) {
                            setTimeout(initializeModal, 100);
                        }
                    });
                }
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    });
</script>
