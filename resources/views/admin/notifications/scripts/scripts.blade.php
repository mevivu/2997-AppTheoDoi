<script>
    $(document).ready(function() {
        // Initialize Select2 Ajax for Customer Search
        if ($('#user_id').length) {
            select2LoadData($('#user_id').data('url'), '#user_id');
        }

        // Live Clock on Mobile Preview
        function updatePreviewClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            $('#preview-clock').text(`${hours}:${minutes}`);
        }
        updatePreviewClock();
        setInterval(updatePreviewClock, 30000);

        // 1. Audience Segment Card Selection
        $('.notification-audience-card').on('click', function() {
            const optionVal = $(this).data('option');
            const summaryText = $(this).data('summary');

            // Update active state
            $('.notification-audience-card').removeClass('active');
            $(this).addClass('active');

            // Set hidden input
            $('#notification_option_input').val(optionVal);

            // Toggle customer / excel box
            if (optionVal == {{ \App\Enums\Notification\NotificationOption::One->value }}) {
                $('#notification-customer-select').slideDown(200);
                $('#notification-excel-file-wrapper').slideUp(150);
                $('#summary-target-badge')
                    .attr('class', 'badge bg-warning-lt text-warning fw-bold fs-12')
                    .html('<i class="ti ti-user-check me-1"></i>' + summaryText);
            } else if (optionVal == {{ \App\Enums\Notification\NotificationOption::Excel->value }}) {
                $('#notification-customer-select').slideUp(150);
                $('#notification-excel-file-wrapper').slideDown(200);
                $('#summary-target-badge')
                    .attr('class', 'badge bg-info-lt text-info fw-bold fs-12')
                    .html('<i class="ti ti-file-spreadsheet me-1"></i>' + summaryText);
            } else {
                $('#notification-customer-select').slideUp(150);
                $('#notification-excel-file-wrapper').slideUp(150);
                $('#summary-target-badge')
                    .attr('class', 'badge bg-success-lt text-success fw-bold fs-12')
                    .html('<i class="ti ti-world me-1"></i>' + summaryText);
            }
        });

        // 2. Live Character Counter & Mobile Preview Binding for Title
        $('#notification_title').on('input', function() {
            const val = $(this).val();
            const len = val.length;
            const counter = $('#title-char-count');
            
            counter.text(`${len}/100`);
            if (len > 80) {
                counter.attr('class', 'char-counter danger');
            } else if (len > 50) {
                counter.attr('class', 'char-counter warning');
            } else {
                counter.attr('class', 'char-counter');
            }

            if (val.trim()) {
                $('#preview-notification-title').text(val);
            } else {
                $('#preview-notification-title').text("{{ __('Tiêu đề thông báo của bạn') }}");
            }
        });

        // 3. Live Character Counter & Mobile Preview Binding for Message
        $('#notification_message').on('input', function() {
            const val = $(this).val();
            const len = val.length;
            const counter = $('#message-char-count');

            counter.text(`${len}/1000`);
            if (len > 900) {
                counter.attr('class', 'char-counter danger');
            } else if (len > 750) {
                counter.attr('class', 'char-counter warning');
            } else {
                counter.attr('class', 'char-counter');
            }

            if (val.trim()) {
                $('#preview-notification-body').text(val);
            } else {
                $('#preview-notification-body').text("{{ __('Nội dung thông báo sẽ xuất hiện trực tiếp tại đây khi bạn soạn thảo ở bên trái...') }}");
            }
        });

        // 4. Quick Message Template Insertion
        $('.quick-template-pill').on('click', function() {
            const title = $(this).data('title');
            const message = $(this).data('message');

            $('#notification_title').val(title).trigger('input');
            $('#notification_message').val(message).trigger('input');

            // Subtle highlight on input cards
            $('#notification_title, #notification_message').addClass('border-primary');
            setTimeout(() => {
                $('#notification_title, #notification_message').removeClass('border-primary');
            }, 1000);
        });

        // 5. Excel Drag & Drop File Upload Handling
        const dropzone = $('#excel-dropzone');
        const fileInput = $('#excel_file');

        function formatBytes(bytes, decimals = 2) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
        }

        function handleFileSelection(files) {
            if (files && files.length > 0) {
                const file = files[0];
                $('#selected-file-name').text(file.name);
                $('#selected-file-size').text(formatBytes(file.size));
                $('#dropzone-empty-state').hide();
                $('#dropzone-selected-state').show();
            } else {
                $('#dropzone-selected-state').hide();
                $('#dropzone-empty-state').show();
            }
        }

        fileInput.on('change', function(e) {
            handleFileSelection(this.files);
        });

        // Remove selected file
        $('#btn-remove-excel-file').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            fileInput.val('');
            $('#dropzone-selected-state').hide();
            $('#dropzone-empty-state').show();
        });

        // Drag & Drop visual state
        dropzone.on('dragover dragenter', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropzone.addClass('dragover');
        });

        dropzone.on('dragleave dragend drop', function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropzone.removeClass('dragover');
        });

        // Trigger on load for old/existing values
        $('#notification_title').trigger('input');
        $('#notification_message').trigger('input');
    });
</script>
