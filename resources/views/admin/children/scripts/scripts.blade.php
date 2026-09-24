<script>
    $(document).ready(function() {
        // 1. Initial child select2 and born radio change
        if ($('#user_id').length && $('#user_id').data('url')) {
            select2LoadData($('#user_id').data('url'), '#user_id');
        }

        $('#is_born').change(function() {
            const isBorn = $('#is_born').val();
            if (isBorn === 'born') {
                $('#date_birthday').removeClass('d-none');
                $('#date_birthday input').attr('required', true);

                $('#due_date').addClass('d-none');
                $('#due_date input').removeAttr('required');
            } else {
                $('#date_birthday').addClass('d-none');
                $('#date_birthday input').removeAttr('required');

                $('#due_date').removeClass('d-none');
                $('#due_date input').attr('required', true);
            }
        });

        // 2. Tab Navigation Hash Support (Direct jump to Phác Đồ)
        var currentHash = window.location.hash;
        if (currentHash === '#tab-child-height-pred' || currentHash === '#content-child-height-pred' || currentHash === '#tab-height-prediction') {
            var targetTabTrigger = document.querySelector('#tab-child-height-pred');
            if (targetTabTrigger) {
                var tabInstance = new bootstrap.Tab(targetTabTrigger);
                tabInstance.show();
            }
        }

        // 3. Phác Đồ Chiều Cao V2 Variables & Initial Data
        @if(isset($children) && $children->id)
            var childId = {{ $children->id }};
            var currentPredHeight = {{ $predHeight ?? 0 }};
            var phacDoChart = null;
            var initialChartData = @json($heightChart ?? null);

            // Function to render ApexCharts for Phác đồ
            function renderPhacDoChart(chartData) {
                if (!chartData || !chartData.prediction_line || chartData.prediction_line.length === 0) {
                    $('#phac-do-apexchart').html('<div class="text-center py-5 text-muted"><i class="ti ti-info-circle fs-2 mb-2"></i><div>Chưa đủ dữ liệu để vẽ biểu đồ phác đồ.</div></div>');
                    return;
                }

                var container = document.querySelector("#phac-do-apexchart");
                if (!container) return;

                var categories = [];
                var predSeries = [];
                var whoSeries = [];
                var targetSeries = [];

                var predLine = chartData.prediction_line || [];
                var whoLine = chartData.who_line || [];
                var targetLine = chartData.target_line || [];
                var hasTarget = targetLine && targetLine.length > 0;

                for (var i = 0; i < predLine.length; i++) {
                    var p = predLine[i];
                    var label = p.is_current ? 'Hiện tại (' + p.age + 't)' : p.age + 't';
                    categories.push(label);
                    predSeries.push(p.height);

                    if (whoLine[i]) {
                        whoSeries.push(whoLine[i].height);
                    } else {
                        whoSeries.push(null);
                    }

                    if (hasTarget && targetLine[i]) {
                        targetSeries.push(targetLine[i].height);
                    }
                }

                var series = [
                    {
                        name: 'Dự đoán chiều cao tự nhiên',
                        data: predSeries
                    },
                    {
                        name: 'Chuẩn WHO',
                        data: whoSeries
                    }
                ];

                var colors = ['#d97706', '#64748b'];
                var strokeDash = [0, 4];

                if (hasTarget) {
                    series.push({
                        name: 'Mục tiêu phác đồ',
                        data: targetSeries
                    });
                    colors.push('#e05263');
                    strokeDash.push(0);
                }

                var options = {
                    series: series,
                    chart: {
                        height: 390,
                        type: 'line',
                        toolbar: {
                            show: true,
                            tools: {
                                download: true,
                                selection: false,
                                zoom: false,
                                zoomin: false,
                                zoomout: false,
                                pan: false,
                                reset: false
                            }
                        },
                        animations: {
                            enabled: true,
                            easing: 'easeinout',
                            speed: 500
                        },
                        fontFamily: 'inherit'
                    },
                    colors: colors,
                    stroke: {
                        width: [2.5, 2.0, 2.5],
                        curve: 'smooth',
                        dashArray: strokeDash
                    },
                    markers: {
                        size: [4, 3, 4],
                        strokeWidth: 1.5,
                        hover: {
                            size: 6
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function(val) {
                            return val ? Math.round(val) + '' : '';
                        },
                        offsetY: -6,
                        style: {
                            fontSize: '10px',
                            fontWeight: 500
                        },
                        background: {
                            enabled: true,
                            padding: 2,
                            borderRadius: 3,
                            opacity: 0.85
                        }
                    },
                    xaxis: {
                        categories: categories,
                        labels: {
                            style: {
                                fontSize: '11px',
                                fontWeight: 500,
                                colors: '#64748b'
                            }
                        },
                        axisBorder: {
                            show: true,
                            color: '#e2e8f0'
                        }
                    },
                    yaxis: {
                        title: {
                            text: 'Chiều cao (cm)',
                            style: {
                                fontSize: '11px',
                                fontWeight: 500,
                                color: '#94a3b8'
                            }
                        },
                        labels: {
                            formatter: function(val) {
                                return Math.round(val) + ' cm';
                            },
                            style: {
                                fontSize: '11px',
                                colors: '#94a3b8'
                            }
                        }
                    },
                    grid: {
                        borderColor: '#f1f5f9',
                        strokeDashArray: 2
                    },
                    tooltip: {
                        shared: true,
                        intersect: false,
                        y: {
                            formatter: function(val) {
                                return val ? val + ' cm' : '--';
                            }
                        }
                    },
                    legend: {
                        show: false
                    }
                };

                if (phacDoChart) {
                    phacDoChart.destroy();
                }
                container.innerHTML = '';
                phacDoChart = new ApexCharts(container, options);
                phacDoChart.render();

                // Render Milestones Table
                renderMilestonesTable(predLine, whoLine, targetLine);
            }

            // Function to render Milestones Table
            function renderMilestonesTable(predLine, whoLine, targetLine) {
                var tbody = document.querySelector("#tbody-milestones");
                if (!tbody) return;

                var html = '';
                var prevPred = null;

                for (var i = 0; i < predLine.length; i++) {
                    var p = predLine[i];
                    var w = whoLine[i] ? whoLine[i].height : null;
                    var t = targetLine && targetLine[i] ? targetLine[i].height : null;

                    var ageLabel = p.is_current 
                        ? '<span class="badge-soft-neutral"><i class="ti ti-pin me-1"></i>Hiện tại (' + p.age + 't)</span>' 
                        : '<span class="fw-semibold">' + p.age + ' tuổi</span>';
                    var isCurrentClass = p.is_current ? 'is-current-row' : '';

                    // Mức tăng/năm
                    var growthDelta = '--';
                    if (prevPred !== null && !p.is_current) {
                        var diff = (p.height - prevPred).toFixed(1);
                        growthDelta = '<span class="text-amber fw-medium">+' + diff + ' cm</span>';
                    } else if (p.is_current) {
                        growthDelta = '<span class="text-muted fs-11">Điểm xuất phát</span>';
                    }
                    prevPred = p.height;

                    // So sánh chuẩn WHO
                    var whoDiffText = '--';
                    if (w !== null) {
                        var dw = (p.height - w).toFixed(1);
                        if (dw > 0) {
                            whoDiffText = '<span class="badge-soft-neutral" style="color: #059669; background: #ecfdf5;">+' + dw + ' cm vs WHO</span>';
                        } else if (dw < 0) {
                            whoDiffText = '<span class="badge-soft-neutral" style="color: #b45309; background: #fffbeb;">' + dw + ' cm vs WHO</span>';
                        } else {
                            whoDiffText = '<span class="badge-soft-neutral">Chuẩn WHO</span>';
                        }
                    }

                    var targetText = t ? '<span class="text-rose fw-semibold">' + t.toFixed(1) + ' cm</span>' : '<span class="text-muted">--</span>';

                    html += '<tr class="' + isCurrentClass + '">' +
                        '<td>' + ageLabel + '</td>' +
                        '<td><span class="text-amber fw-semibold">' + p.height.toFixed(1) + ' cm</span></td>' +
                        '<td>' + growthDelta + '</td>' +
                        '<td><span class="text-muted fw-semibold">' + (w ? w.toFixed(1) + ' cm' : '--') + '</span></td>' +
                        '<td>' + targetText + '</td>' +
                        '<td>' + whoDiffText + '</td>' +
                        '</tr>';
                }

                tbody.innerHTML = html;
            }

            // Render on page load if initialChartData is present
            if (initialChartData) {
                renderPhacDoChart(initialChartData);
            }

            // Resize ApexChart when switching tabs so it displays with correct width
            $('button[data-bs-target="#content-child-height-pred"]').on('shown.bs.tab', function() {
                if (phacDoChart) {
                    phacDoChart.render();
                } else if (initialChartData) {
                    renderPhacDoChart(initialChartData);
                }
            });

            // ==========================================
            // PQ 5-ATTRIBUTE RADAR CHART (ApexCharts)
            // ==========================================
            var initialPqData = @json($pqOverall ?? null);
            var pqRadarChart = null;

            function renderPqRadarChart(pqData) {
                if (!pqData) return;
                var container = document.querySelector("#pq-radar-chart");
                if (!container) return;

                var currentHeightScore = parseFloat(pqData.current_height_percent) || 0;
                var adultHeightScore = parseFloat(pqData.height_adulthood) || 0;
                var bmiScore = parseFloat(pqData.bmi_percent) || 0;
                var strengthScore = parseFloat(pqData.strength_percent) || 0;
                var enduranceScore = parseFloat(pqData.endurance_percent) || 0;

                var options = {
                    series: [{
                        name: 'Điểm thể chất',
                        data: [currentHeightScore, adultHeightScore, bmiScore, strengthScore, enduranceScore]
                    }],
                    chart: {
                        height: 350,
                        type: 'radar',
                        toolbar: { show: false },
                        dropShadow: {
                            enabled: true,
                            blur: 4,
                            left: 1,
                            top: 1,
                            opacity: 0.1
                        }
                    },
                    colors: ['#16a34a'],
                    stroke: {
                        width: 2.5,
                        colors: ['#16a34a']
                    },
                    fill: {
                        opacity: 0.25,
                        colors: ['#22c55e']
                    },
                    markers: {
                        size: 4,
                        colors: ['#16a34a'],
                        strokeColors: '#ffffff',
                        strokeWidth: 2,
                        hover: { size: 6 }
                    },
                    xaxis: {
                        categories: [
                            'Chiều cao hiện tại (' + currentHeightScore + ')',
                            'Chiều cao trưởng thành (' + adultHeightScore + ')',
                            'BMI / Cân nặng (' + bmiScore + ')',
                            'Sức mạnh (' + strengthScore + ')',
                            'Sức bền (' + enduranceScore + ')'
                        ],
                        labels: {
                            show: true,
                            style: {
                                colors: ['#0f172a', '#0f172a', '#0f172a', '#0f172a', '#0f172a'],
                                fontSize: '11px',
                                fontWeight: 600
                            }
                        }
                    },
                    yaxis: {
                        min: 0,
                        max: 10,
                        tickAmount: 5,
                        labels: {
                            formatter: function(val) {
                                return Math.round(val);
                            },
                            style: {
                                colors: '#94a3b8',
                                fontSize: '10px'
                            }
                        }
                    },
                    plotOptions: {
                        radar: {
                            polygons: {
                                strokeColors: '#e2e8f0',
                                fill: {
                                    colors: ['#f8fafc', '#ffffff']
                                }
                            }
                        }
                    },
                    tooltip: {
                        y: {
                            formatter: function(val) {
                                return val + ' / 10 điểm';
                            }
                        }
                    }
                };

                if (pqRadarChart) {
                    pqRadarChart.destroy();
                }
                container.innerHTML = '';
                pqRadarChart = new ApexCharts(container, options);
                pqRadarChart.render();
            }

            // Render on page load if initialPqData is present
            if (initialPqData) {
                renderPqRadarChart(initialPqData);
            }

            // Render / Resize Radar Chart when switching to Assessment Tab
            $('button[data-bs-target="#content-child-assessment"]').on('shown.bs.tab', function() {
                if (pqRadarChart) {
                    pqRadarChart.render();
                } else if (initialPqData) {
                    renderPqRadarChart(initialPqData);
                }
            });

            // 4. Puberty Chips selection
            $('.puberty-chip').on('click', function() {
                $('.puberty-chip').removeClass('active');
                $(this).addClass('active');

                var months = $(this).data('months');
                $('#input_puberty_months').val(months);

                // Auto-trigger prediction
                $('#btn-predict-v2').trigger('click');
            });

            $('#input_puberty_months').on('input change', function() {
                var val = parseInt($(this).val()) || 0;
                $('.puberty-chip').removeClass('active');
                $('.puberty-chip[data-months="' + val + '"]').addClass('active');
            });

            // 5. Button "Dự đoán chiều cao V2" click handler
            $('#btn-predict-v2').on('click', function() {
                var pubertyMonths = parseFloat($('#input_puberty_months').val()) || 0;
                var $btn = $(this);
                var $spinner = $('#predict-spinner');
                var $icon = $('#predict-icon');

                $btn.prop('disabled', true);
                $spinner.removeClass('d-none');
                $icon.addClass('d-none');

                $.ajax({
                    url: "{{ route('admin.children.predictHeightV2') }}",
                    type: 'POST',
                    data: {
                        child_id: childId,
                        puberty_months: pubertyMonths,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        if (res.status === 200 && res.data) {
                            var data = res.data;
                            currentPredHeight = data.predicting_adult_height;

                            // Update UI Step 2 cards
                            $('#display-pred-height').text(Math.round(data.predicting_adult_height));
                            $('#ref-pred-height').text(Math.round(data.predicting_adult_height));

                            var whoDiff = data.height_comparison.height_who_current;
                            $('#display-who-diff').text((whoDiff > 0 ? '+' : '') + parseFloat(whoDiff).toFixed(1));
                            $('#display-who-msg').text(data.height_comparison.message);
                            $('#display-speed-change').text(parseFloat(data.speed_change).toFixed(1));

                            // Update Mini Header Card
                            $('.child-hero-stats-grid .text-info').text(Math.round(data.predicting_adult_height) + ' cm');

                            // Update default target input if needed
                            var currentTarget = parseFloat($('#input_target_height').val()) || 0;
                            if (currentTarget <= currentPredHeight) {
                                $('#input_target_height').val(Math.round(currentPredHeight + 5));
                            }

                            // Automatically refresh phác đồ chart with the new puberty prediction
                            runPhacDoChart(false);
                        } else {
                            alert(res.message || 'Không thể tính toán dự báo.');
                        }
                    },
                    error: function(xhr) {
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Lỗi khi gọi API dự báo.';
                        alert(msg);
                    },
                    complete: function() {
                        $btn.prop('disabled', false);
                        $spinner.addClass('d-none');
                        $icon.removeClass('d-none');
                    }
                });
            });

            // 6. Target Height Chips selection
            $('.target-chip').on('click', function() {
                $('.target-chip').removeClass('active');
                $(this).addClass('active');

                var delta = parseInt($(this).data('delta')) || 5;
                var basePred = currentPredHeight > 0 ? currentPredHeight : (parseFloat($('#display-pred-height').text()) || 165);
                var newTarget = Math.round(basePred + delta);
                $('#input_target_height').val(newTarget);

                // Auto-run chart
                runPhacDoChart(true);
            });

            // 7. Button "Chạy Phác Đồ" click handler
            $('#btn-run-chart-v2').on('click', function() {
                runPhacDoChart(true);
            });

            function runPhacDoChart(animate) {
                var pubertyMonths = parseFloat($('#input_puberty_months').val()) || 0;
                var targetHeight = parseFloat($('#input_target_height').val()) || null;

                var $btn = $('#btn-run-chart-v2');
                var $spinner = $('#chart-spinner');
                var $icon = $('#chart-icon');

                $btn.prop('disabled', true);
                $spinner.removeClass('d-none');
                $icon.addClass('d-none');

                $.ajax({
                    url: "{{ route('admin.children.heightChartV2') }}",
                    type: 'POST',
                    data: {
                        child_id: childId,
                        puberty_months: pubertyMonths,
                        target_height: targetHeight,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        if (res.status === 200 && res.data) {
                            var data = res.data;
                            renderPhacDoChart(data);

                            // Update Regimen Advice UI if present
                            if (data.regimen_advice) {
                                var advice = data.regimen_advice;
                                if (advice.target_header) {
                                    $('#advice-header-title').text(advice.target_header);
                                }
                                if (advice.bmi) {
                                    $('#display-bmi-val').text(advice.bmi);
                                }
                                if (advice.bmi_assessment) {
                                    $('#display-bmi-status').text(advice.bmi_assessment);
                                    $('#advice-bmi-badge').text(advice.bmi_assessment);
                                }
                                if (advice.bmi_advice) {
                                    $('#advice-bmi-text').text(advice.bmi_advice);
                                }
                            }
                        } else {
                            alert(res.message || 'Không thể tải phác đồ.');
                        }
                    },
                    error: function(xhr) {
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Lỗi khi tải biểu đồ phác đồ.';
                        alert(msg);
                    },
                    complete: function() {
                        $btn.prop('disabled', false);
                        $spinner.addClass('d-none');
                        $icon.removeClass('d-none');
                    }
                });
            }

            // 8. Debug Dữ Liệu & Công Thức Modal Handlers
            var latestDebugData = null;

            $('#btn-open-debug-modal').on('click', function() {
                var pubertyMonths = parseFloat($('#input_puberty_months').val()) || 0;
                var targetHeight = parseFloat($('#input_target_height').val()) || null;

                $('#debug-calc-steps-loading').removeClass('d-none');
                $('#debug-calc-steps-content').addClass('d-none');

                $.ajax({
                    url: "{{ route('admin.children.debugHeightV2') }}",
                    type: 'POST',
                    data: {
                        child_id: childId,
                        puberty_months: pubertyMonths,
                        target_height: targetHeight,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        if (res.status === 200 && res.data) {
                            latestDebugData = res.data;
                            renderDebugModal(latestDebugData);
                        } else {
                            alert(res.message || 'Không thể tải dữ liệu chẩn đoán.');
                        }
                    },
                    error: function(xhr) {
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Lỗi khi gọi API chẩn đoán.';
                        alert(msg);
                    },
                    complete: function() {
                        $('#debug-calc-steps-loading').addClass('d-none');
                        $('#debug-calc-steps-content').removeClass('d-none');
                    }
                });
            });

            function renderDebugModal(data) {
                // Render Tab 1: Luồng tính toán từng bước
                var c = data.child_info;
                var p = data.parents_info;
                var s = data.speed_calculation;
                var pb = data.puberty_calculation;
                var a = data.adulthood_calculation;
                var t = data.target_calculation;
                var b = data.bmi_calculation;

                var htmlSteps = `
                    <div class="row g-3">
                        <!-- Bước 1: Thông tin bé -->
                        <div class="col-12 col-md-6">
                            <div class="card p-3 border border-light-subtle rounded-3 bg-white h-100">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge bg-indigo-soft text-indigo fw-semibold">BƯỚC 1: ĐẦU VÀO TRẺ EM</span>
                                    <span class="fs-11 text-muted">ID #${c.id}</span>
                                </div>
                                <div class="fs-13 fw-semibold text-slate mb-1">${c.name} (${c.gender})</div>
                                <div class="fs-12 text-muted mb-2">Ngày sinh: ${c.birthday || '--'}</div>
                                <div class="p-2 rounded-2 bg-light-subtle border border-light-subtle fs-12">
                                    <div>• Số ngày sống: <strong>${c.days_lived} ngày</strong></div>
                                    <div>• Tuổi chính xác: <strong>${c.current_age_years} tuổi</strong> (${c.days_lived} / 365.3)</div>
                                    <div>• Tháng tuổi quy đổi: <strong>${c.current_age_months} tháng</strong></div>
                                </div>
                            </div>
                        </div>

                        <!-- Bước 2: Di truyền cha mẹ -->
                        <div class="col-12 col-md-6">
                            <div class="card p-3 border border-light-subtle rounded-3 bg-white h-100">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge bg-indigo-soft text-indigo fw-semibold">BƯỚC 2: YẾU TỐ DI TRUYỀN</span>
                                    <span class="fs-11 text-muted">Mid-parental Height</span>
                                </div>
                                <div class="fs-12 text-muted mb-2">Chiều cao Bố: <strong>${p.father_height || '--'} cm</strong> | Mẹ: <strong>${p.mother_height || '--'} cm</strong></div>
                                <div class="p-2 rounded-2 bg-light-subtle border border-light-subtle fs-12 mb-2">
                                    <div class="text-muted fs-11 text-uppercase fw-semibold mb-1">Công thức di truyền:</div>
                                    <code class="text-indigo fs-12">${p.formula}</code>
                                </div>
                                <div class="fs-12 text-slate mb-1">
                                    • Chiều cao di truyền lý thuyết: <strong class="text-indigo fs-13">${p.mid_parent_height ? p.mid_parent_height + ' cm' : 'Chưa xác định'}</strong>
                                </div>
                                ${p.genetic_ratio ? `
                                <div class="fs-12 text-slate mb-1">
                                    • Tỷ lệ Di truyền / WHO: <strong style="color: #0891b2;">${p.genetic_ratio}</strong> (${p.mid_parent_height} cm / ${p.who_adult_height} cm WHO 19t)
                                </div>
                                <div class="fs-11 text-muted">
                                    • Trọng số kết hợp: <span class="fw-semibold text-amber">90% Dự đoán thực tế</span> + <span class="fw-semibold" style="color: #0891b2;">10% Tiềm năng di truyền</span>
                                </div>
                                ` : ''}
                            </div>
                        </div>

                        <!-- Bước 3: Tốc độ tăng trưởng -->
                        <div class="col-12">
                            <div class="card p-3 border border-light-subtle rounded-3 bg-white">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge bg-amber-soft text-amber fw-semibold">BƯỚC 3: TỐC ĐỘ TĂNG TRƯỞNG & QUY TẮC CHẶN TRẦN</span>
                                    <span class="badge bg-light text-muted fs-11">${s.clamped_rule}</span>
                                </div>
                                <div class="row g-2 mb-2">
                                    <div class="col-12 col-md-6">
                                        <div class="p-2 rounded-2 bg-light-subtle border border-light-subtle fs-12">
                                            <div class="text-muted fs-11 fw-semibold mb-1">ĐỢT ĐO PQ MỚI NHẤT:</div>
                                            <div>Ngày: <strong>${s.latest_pq.date}</strong> | Cao: <strong>${s.latest_pq.height} cm</strong> | Nặng: <strong>${s.latest_pq.weight} kg</strong></div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="p-2 rounded-2 bg-light-subtle border border-light-subtle fs-12">
                                            <div class="text-muted fs-11 fw-semibold mb-1">ĐỢT ĐO PQ CŨ TRONG 1 NĂM QUA:</div>
                                            <div>${s.oldest_pq_in_year ? `Ngày: <strong>${s.oldest_pq_in_year.date}</strong> | Cao: <strong>${s.oldest_pq_in_year.height} cm</strong>` : '<span class="text-muted">Không có bản ghi cách 1 năm</span>'}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-2 rounded-2 bg-light-subtle border border-light-subtle fs-12 mb-2">
                                    <div>• Khoảng cách giữa 2 lần đo: <strong>${s.count_days} ngày</strong> | Mức tăng thô: <strong>${s.raw_height_diff} cm</strong></div>
                                    <div>• Công thức tốc độ tăng trưởng/năm: <code class="text-amber">${s.speed_formula}</code></div>
                                    <div>• Tốc độ tính toán thô: <strong>${s.raw_speed_annualized} cm/năm</strong></div>
                                    <div>• Áp dụng luật giới hạn trần: <strong>Max = 6.5 cm/năm</strong> -> Tốc độ sau chặn: <strong>${s.clamped_speed} cm/năm</strong></div>
                                    ${s.is_fallback_used ? `<div class="text-amber mt-1"><i class="ti ti-alert-circle me-1"></i>Tốc độ thô <= 0 -> Áp dụng Fallback theo chuẩn WHO lứa tuổi: <strong>${s.who_current_speed} cm/năm</strong></div>` : ''}
                                </div>
                                <div class="fs-13 fw-semibold text-slate">
                                    ==> Tốc độ tăng trưởng áp dụng (Effective Speed): <strong class="text-amber fs-15">${s.effective_speed} cm/năm</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Bước 4: Điều chỉnh dậy thì -->
                        <div class="col-12 col-md-6">
                            <div class="card p-3 border border-light-subtle rounded-3 bg-white h-100">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge bg-purple-soft text-purple fw-semibold">BƯỚC 4: ĐIỀU CHỈNH DẬY THÌ</span>
                                    <span class="fs-11 text-muted">V2 Algorithm</span>
                                </div>
                                <div class="fs-12 text-muted mb-2">Số tháng dậy thì nhập: <strong>${pb.input_months} tháng</strong> -> Quy đổi: <strong>${pb.puberty_years} năm</strong></div>
                                <div class="p-2 rounded-2 bg-light-subtle border border-light-subtle fs-12 mb-2">
                                    <div>• Tuổi chốt tăng trưởng chuẩn (${c.gender}): <strong>${pb.base_puberty_end_age} tuổi</strong></div>
                                    <div>• Công thức tuổi chốt thực tế: <code class="text-purple">${pb.formula}</code></div>
                                    <div>• Tuổi kết thúc dậy thì: <strong>${pb.puberty_end_age} tuổi</strong></div>
                                    <div>• Thời gian tăng tốc còn lại: <strong>${pb.years_remaining} năm</strong> (${pb.puberty_end_age} - ${c.current_age_years})</div>
                                </div>
                                <div class="fs-12 text-slate">
                                    Tăng trưởng dự kiến trong giai đoạn dậy thì: <strong class="text-purple">${pb.predicted_puberty_growth} cm</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Bước 5: Dự đoán tuổi 19 -->
                        <div class="col-12 col-md-6">
                            <div class="card p-3 border border-light-subtle rounded-3 bg-white h-100">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge bg-indigo-soft text-indigo fw-semibold">BƯỚC 5: DỰ ĐOÁN TUỔI 19</span>
                                    <span class="badge bg-light text-muted fs-11">Mốc 19 tuổi</span>
                                </div>
                                <div class="fs-12 text-muted mb-2">Mô phỏng chu kỳ: <strong>Từ tuổi ${a.start_age} đến ${a.max_age}</strong></div>
                                <div class="p-2 rounded-2 bg-light-subtle border border-light-subtle fs-12 mb-2">
                                    <div>• Sau tuổi hết dậy thì (${pb.puberty_end_age}t): Cộng delta WHO dịch chuyển +${pb.puberty_years} năm.</div>
                                    <div>• Chiều cao chuẩn WHO mốc 19 tuổi: <strong>${a.who_adult_height} cm</strong></div>
                                    <div>• Chênh lệch so với WHO lúc 19 tuổi: <strong>${(a.diff_with_who > 0 ? '+' : '') + a.diff_with_who} cm</strong></div>
                                </div>
                                <div class="fs-13 fw-semibold text-slate">
                                    ==> Dự đoán chiều cao trưởng thành (V2): <strong class="text-indigo fs-16">${a.predicted_adult_height} cm</strong> (±5 cm)
                                </div>
                            </div>
                        </div>

                        <!-- Bước 6: Phân bổ mục tiêu -->
                        <div class="col-12 col-md-6">
                            <div class="card p-3 border border-light-subtle rounded-3 bg-white h-100">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge bg-rose-soft text-rose fw-semibold">BƯỚC 6: PHÂN BỔ MỤC TIÊU</span>
                                    <span class="fs-11 text-muted">Goal Curve</span>
                                </div>
                                <div class="fs-12 text-muted mb-2">Mục tiêu nhập: <strong>${t.input_target ? t.input_target + ' cm' : 'Chưa đặt'}</strong> -> Mục tiêu tính: <strong>${t.final_target ? t.final_target + ' cm' : '--'}</strong></div>
                                <div class="p-2 rounded-2 bg-light-subtle border border-light-subtle fs-12 mb-2">
                                    <div>• Tổng tăng trưởng theo dự đoán: <strong>${t.pred_total_growth} cm</strong></div>
                                    <div>• Tổng tăng trưởng cần đạt theo mục tiêu: <strong>${t.target_total_growth} cm</strong></div>
                                    <div>• Công thức đường mục tiêu: <code class="text-rose">${t.formula}</code></div>
                                </div>
                                <div class="fs-12 text-slate">
                                    Nguyên tắc: <span class="text-muted">Mục tiêu >= Dự đoán, bám theo tỷ lệ tiến trình đường tăng trưởng tự nhiên.</span>
                                </div>
                            </div>
                        </div>

                        <!-- Bước 7: Thể trạng BMI -->
                        <div class="col-12 col-md-6">
                            <div class="card p-3 border border-light-subtle rounded-3 bg-white h-100">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="badge bg-emerald-soft text-emerald fw-semibold">BƯỚC 7: ĐÁNH GIÁ THỂ TRẠNG BMI</span>
                                    <span class="badge bg-light text-muted fs-11">WHO Z-Score</span>
                                </div>
                                <div class="fs-12 text-muted mb-2">Chỉ số BMI hiện tại: <strong class="fs-14 text-emerald">${b.current_bmi}</strong> (Bảng lứa tuổi: <strong>${b.table_age_years} tuổi</strong>)</div>
                                ${b.standard_row ? `
                                <div class="p-2 rounded-2 bg-light-subtle border border-light-subtle fs-11 mb-2">
                                    <div class="row text-center g-1">
                                        <div class="col"><span class="text-muted">-2SD</span><br><strong>${b.standard_row.z_minus_2}</strong></div>
                                        <div class="col"><span class="text-muted">-1SD</span><br><strong>${b.standard_row.z_minus_1}</strong></div>
                                        <div class="col text-emerald"><span class="fw-bold">Chuẩn (0)</span><br><strong>${b.standard_row.z_0}</strong></div>
                                        <div class="col"><span class="text-muted">+1SD</span><br><strong>${b.standard_row.z_plus_1}</strong></div>
                                        <div class="col"><span class="text-muted">+2SD</span><br><strong>${b.standard_row.z_plus_2}</strong></div>
                                    </div>
                                </div>
                                ` : ''}
                                <div class="fs-13 fw-semibold text-slate">
                                    ==> Đánh giá thể trạng: <span class="badge-soft-neutral text-emerald fw-bold fs-12">${b.assessment}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                $('#debug-calc-steps-content').html(htmlSteps);

                // Render Tab 2: Bảng mô phỏng
                var tbodySim = $('#debug-tbody-sim');
                var simRows = '';
                (data.simulation_matrix || []).forEach(function(row) {
                    var phaseClass = row.is_current ? 'badge-soft-neutral text-success fw-bold' : (row.phase.indexOf('Dậy thì') !== -1 ? 'badge-soft-amber' : 'badge-soft-neutral');
                    var currentClass = row.is_current ? 'is-current-row' : '';
                    simRows += `
                        <tr class="${currentClass}">
                            <td><strong>${row.label}</strong></td>
                            <td><span class="${phaseClass}">${row.phase}</span></td>
                            <td class="text-start fs-11"><code class="text-muted">${row.formula}</code></td>
                            <td>${row.growth_delta > 0 ? '<span class="text-amber fw-semibold">+' + row.growth_delta + ' cm</span>' : '--'}</td>
                            <td><strong style="color: #d97706;">${row.pred_height} cm</strong></td>
                            <td><span class="text-muted">${row.who_height} cm</span></td>
                            <td>${row.target_height ? '<strong style="color: #e05263;">' + row.target_height + ' cm</strong>' : '--'}</td>
                            <td>${row.genetic_height ? '<span class="fw-semibold" style="color: #0891b2;">' + row.genetic_height + ' cm</span>' : '<span class="text-muted">--</span>'}</td>
                            <td>${row.final_pred_height ? '<strong style="color: #059669;">' + row.final_pred_height + ' cm</strong>' : '<span class="text-muted">--</span>'}</td>
                        </tr>
                    `;
                });
                tbodySim.html(simRows);

                // Render Tab 3: Lịch sử PQ & JSON
                var tbodyPq = $('#debug-tbody-pq-history');
                var pqRows = '';
                (data.raw_pq_history || []).forEach(function(item) {
                    pqRows += `
                        <tr>
                            <td>#${item.id}</td>
                            <td><strong>${item.assessment_date}</strong></td>
                            <td>${item.age_month} tháng</td>
                            <td><strong class="text-primary">${item.height} cm</strong></td>
                            <td>${item.weight} kg</td>
                            <td><span class="badge-soft-purple">${item.bmi}</span></td>
                        </tr>
                    `;
                });
                tbodyPq.html(pqRows || '<tr><td colspan="6" class="text-muted py-3">Chưa có bản ghi PQ</td></tr>');

                $('#debug-json-viewer').text(JSON.stringify(data, null, 2));
            }

            // Copy JSON handler
            $('#btn-copy-debug-json').on('click', function() {
                if (!latestDebugData) return;
                var jsonStr = JSON.stringify(latestDebugData, null, 2);
                navigator.clipboard.writeText(jsonStr).then(function() {
                    var $text = $('#btn-copy-text');
                    $text.text('Đã chép!');
                    setTimeout(function() {
                        $text.text('Sao chép JSON');
                    }, 2000);
                });
            });
        @endif
    });
</script>
