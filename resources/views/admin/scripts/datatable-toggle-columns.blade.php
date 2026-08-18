<x-input type="hidden" name="id_table" :value="$id_table" />
<style>
    table.dataTable tbody tr.selected-row {
        background-color: #d4edda !important;
        color: #155724 !important;
    }

    /* ===== MOBILE CUSTOM TOOLBAR ===== */
    .mobile-toolbar {
        display: none;
    }

    @media (max-width: 991.98px) {
        /* Ẩn toolbar desktop trên mobile */
        .toggle-columns-table,
        .dataTables_wrapper > .dt-buttons {
            display: none !important;
        }

        /* Hiển thị toolbar mobile chuẩn */
        .mobile-toolbar {
            display: flex !important;
            align-items: center;
            gap: 8px;
            padding: 8px 0;
            margin-bottom: 12px;
            flex-wrap: nowrap;
            justify-content: space-between;
        }

        .mobile-toolbar .mt-view-toggle {
            display: inline-flex;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            overflow: hidden;
            flex-shrink: 0;
        }

        .mobile-toolbar .mt-view-toggle .mt-view-btn {
            border: none;
            background: #fff;
            padding: 7px 12px;
            font-size: 15px;
            color: #94a3b8;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            line-height: 1;
        }

        .mobile-toolbar .mt-view-toggle .mt-view-btn.active {
            background: #2563eb;
            color: #fff;
        }

        .mobile-toolbar .mt-view-toggle .mt-view-btn + .mt-view-btn {
            border-left: 1px solid #e2e8f0;
        }

        .mobile-toolbar .mt-btn {
            border: 1px solid #e2e8f0;
            background: #fff;
            border-radius: 8px;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: #64748b;
            cursor: pointer;
            transition: all 0.2s;
            flex-shrink: 0;
            position: relative;
        }

        .mobile-toolbar .mt-btn:hover,
        .mobile-toolbar .mt-btn:active {
            background: #f1f5f9;
            color: #2563eb;
            border-color: #bfdbfe;
        }

        .mobile-toolbar .mt-btn.mt-btn-success {
            background: #10b981;
            color: #fff;
            border-color: #10b981;
        }

        .mobile-toolbar .mt-btn.mt-btn-success:hover {
            background: #059669;
        }

        .mobile-toolbar .mt-page-length {
            flex: 1;
            min-width: 0;
            margin-left: 2px;
            padding-left: 4px;
        }

        .mobile-toolbar .mt-page-length select {
            width: 100%;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 6px 8px;
            font-size: 13px;
            font-weight: 500;
            color: #334155;
            background: #fff;
            height: 36px;
            cursor: pointer;
            appearance: auto;
        }

        .mobile-toolbar .mt-page-length select:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.15);
        }

        .mobile-toolbar .mt-col-wrapper {
            position: relative;
            display: inline-block;
        }

        .mobile-toolbar .mt-col-dropdown {
            position: fixed;
            top: auto;
            left: 12px;
            right: 12px;
            margin-top: 6px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.2);
            padding: 10px;
            max-height: 300px;
            overflow-y: auto;
            width: auto;
            z-index: 100010;
            display: none;
            -webkit-overflow-scrolling: touch;
        }

        .mobile-toolbar .mt-col-dropdown.show {
            display: block;
        }

        .mobile-toolbar .mt-col-dropdown label {
            display: flex;
            align-items: center;
            padding: 10px 12px;
            border-radius: 8px;
            font-size: 14px;
            color: #334155;
            cursor: pointer;
            gap: 10px;
            margin-bottom: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            user-select: none;
            -webkit-tap-highlight-color: transparent;
        }

        .mobile-toolbar .mt-col-dropdown label:active,
        .mobile-toolbar .mt-col-dropdown label:hover {
            background: #f1f5f9;
        }

        .mobile-toolbar .mt-col-dropdown input[type="checkbox"] {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
            cursor: pointer;
        }
    }
</style>
<script>
    $(document).ready(function() {
        // define columns for the datatables
        columns = window.LaravelDataTables[$("input[name=id_table]").val()].columns();
        toggleColumnsDatatable(columns);

        const tableId = '#{{ $dataTable->getTableAttribute('id') }}';
        const tableName = $("input[name=id_table]").val();

        // Reload datatable handler
        $(document).on('click', '.btn-reload-datatable', function(e) {
            e.preventDefault();
            var dt = window.LaravelDataTables[tableName];
            if (dt) {
                const $icon = $(this).find('i');
                $icon.addClass('spin-animation');
                dt.ajax.reload(function() {
                    $icon.removeClass('spin-animation');
                }, false);
            }
        });

        $(tableId + ' tbody').on('change', 'input[type="checkbox"]', function() {
            const row = $(this).closest('tr');
            if (this.checked) {
                row.addClass('selected-row');
            } else {
                row.removeClass('selected-row');
            }
        });

        $(tableId + ' thead').on('change', '.check-all', function() {
            const checked = this.checked;
            $(tableId + ' tbody input[type="checkbox"]').prop('checked', checked).trigger('change');
        });

        $(tableId + ' tbody').on('change', 'input[type="checkbox"]', function() {
            const allChecked = $(tableId + ' tbody input[type="checkbox"]:not(:checked)').length === 0;
            $(tableId + ' thead .check-all').prop('checked', allChecked);
        });

        // ===== CUSTOM MOBILE TOOLBAR =====
        var mobileToolbarRetries = 0;
        function buildMobileToolbar() {
            if ($('.mobile-toolbar').length || $(window).width() >= 992) return;

            var dt = window.LaravelDataTables[tableName];
            if (!dt) {
                // Retry up to 10 times if DataTable isn't ready yet
                if (mobileToolbarRetries < 10) {
                    mobileToolbarRetries++;
                    setTimeout(buildMobileToolbar, 300);
                }
                return;
            }

            // Get current page length
            var currentLen = dt.page.len();

            // Get current view mode
            var savedMode = localStorage.getItem('datatable_view_mode') || localStorage.getItem('datatable-view-mode') || 'table';

            var html = '<div class="mobile-toolbar">';

            // 1. View toggle (Table / Grid)
            html += '<div class="mt-view-toggle">';
            html += '<button class="mt-view-btn' + (savedMode === 'table' ? ' active' : '') + '" data-mode="table" title="Bảng"><i class="ti ti-table"></i></button>';
            html += '<button class="mt-view-btn' + (savedMode === 'grid' ? ' active' : '') + '" data-mode="grid" title="Thẻ"><i class="ti ti-layout-grid"></i></button>';
            html += '</div>';

            // 2. Column toggle button (wrapped to prevent nesting interactive dropdown inside button)
            html += '<div class="mt-col-wrapper">';
            html += '<button type="button" class="mt-btn mt-btn-success mt-col-toggle" title="Cột"><i class="ti ti-columns"></i></button>';
            html += '<div class="mt-col-dropdown"></div>';
            html += '</div>';

            // 3. Reload button
            html += '<button class="mt-btn mt-reload-btn" title="Tải lại"><i class="ti ti-refresh"></i></button>';

            // 4. Page length select
            html += '<div class="mt-page-length">';
            html += '<select class="mt-page-select">';
            [10, 25, 50, 100].forEach(function(val) {
                html += '<option value="' + val + '"' + (val === currentLen ? ' selected' : '') + '>' + val + ' dòng</option>';
            });
            html += '</select>';
            html += '</div>';

            html += '</div>';

            // Insert before the table - try multiple ways to find the right container
            var $container = null;

            // Method 1: Find by table ID
            var $table = $(tableId);
            if ($table.length) {
                $container = $table.closest('.card-body');
            }

            // Method 2: Find by DataTables wrapper
            if (!$container || !$container.length) {
                $container = $('.dataTables_wrapper').first().closest('.card-body');
            }

            // Method 3: Find by table-responsive class
            if (!$container || !$container.length) {
                $container = $('.table-responsive').first().closest('.card-body');
            }

            if ($container && $container.length) {
                $container.find('.table-responsive, .dataTables_wrapper').first().before(html);
            }

            // --- Event handlers ---

            // View mode toggle
            $(document).on('click', '.mt-view-btn', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var mode = $(this).data('mode');
                $('.mt-view-btn').removeClass('active');
                $(this).addClass('active');
                // Trigger original view mode button or direct mode application
                if ($('.btn-datatable-mode[data-mode="' + mode + '"]').length) {
                    $('.btn-datatable-mode[data-mode="' + mode + '"]').trigger('click');
                } else if (typeof applyDatatableViewMode === 'function') {
                    applyDatatableViewMode(mode);
                } else {
                    const $tableWrapper = $('.table-responsive');
                    if (mode === 'grid') {
                        $tableWrapper.addClass('datatable-grid-active');
                    } else {
                        $tableWrapper.removeClass('datatable-grid-active');
                    }
                    localStorage.setItem('datatable_view_mode', mode);
                }
            });

            // Reload
            $(document).on('click', '.mt-reload-btn', function(e) {
                e.preventDefault();
                var dt = window.LaravelDataTables[tableName];
                if (dt) {
                    const $icon = $(this).find('i');
                    $icon.addClass('spin-animation');
                    dt.ajax.reload(function() {
                        $icon.removeClass('spin-animation');
                    }, false);
                }
            });

            // Page length
            $(document).on('change', '.mt-page-select', function() {
                var val = parseInt($(this).val());
                var dt = window.LaravelDataTables[tableName];
                if (dt) {
                    dt.page.len(val).draw();
                }
            });

            // Column toggle dropdown
            $(document).on('click', '.mt-col-toggle', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var $wrapper = $(this).closest('.mt-col-wrapper');
                var $dropdown = $wrapper.find('.mt-col-dropdown');

                if ($dropdown.hasClass('show')) {
                    $dropdown.removeClass('show');
                    return;
                }

                // Build column list
                var dt = window.LaravelDataTables[tableName];
                if (!dt) return;

                var cols = dt.columns().header().map(function(d) { return $.trim(d.textContent); }).toArray();
                var colHtml = '';
                $.each(cols, function(idx, name) {
                    if (!name) return;
                    var isVisible = dt.column(idx).visible();
                    colHtml += '<label><input type="checkbox" class="mt-col-check form-check-input" data-col="' + idx + '"' + (isVisible ? ' checked' : '') + '> <span>' + name + '</span></label>';
                });
                $dropdown.html(colHtml).addClass('show');
            });

            // Stop click propagation inside dropdown so it doesn't close on interaction
            $(document).on('click', '.mt-col-dropdown', function(e) {
                e.stopPropagation();
            });

            // Column visibility change
            $(document).on('change', '.mt-col-check', function(e) {
                e.stopPropagation();
                var colIdx = parseInt($(this).attr('data-col'));
                var isChecked = $(this).is(':checked');
                var dt = window.LaravelDataTables[tableName];
                
                if (dt) {
                    dt.column(colIdx).visible(isChecked);
                } else if (typeof columns !== 'undefined' && columns) {
                    columns.column(colIdx).visible(isChecked);
                }

                // Sync desktop column toggles
                $('.toggle-vis[data-column="' + colIdx + '"]').prop('checked', isChecked);

                // Re-bind grid data labels if grid mode is active
                if (typeof bindDatatableGridDataLabels === 'function') {
                    bindDatatableGridDataLabels();
                }
            });

            // Close dropdown when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.mt-col-wrapper').length) {
                    $('.mt-col-dropdown').removeClass('show');
                }
            });
        }

        // Build mobile toolbar after DataTables init - multiple attempts
        setTimeout(buildMobileToolbar, 200);
        setTimeout(buildMobileToolbar, 600);
        setTimeout(buildMobileToolbar, 1200);

        $(document).on('init.dt', function() {
            setTimeout(buildMobileToolbar, 100);
            // Fix: Mark table-responsive as ready to enable scrolling after DataTable init
            $('.table-responsive').addClass('dt-ready');
        });

        // Also mark as ready after a short delay as fallback
        setTimeout(function() {
            $('.table-responsive').addClass('dt-ready');
        }, 500);
    });
</script>
