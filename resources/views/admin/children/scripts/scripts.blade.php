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

                var colors = ['#ea580c', '#0284c7'];
                var strokeDash = [0, 5];

                if (hasTarget) {
                    series.push({
                        name: 'Mục tiêu phác đồ',
                        data: targetSeries
                    });
                    colors.push('#dc2626');
                    strokeDash.push(0);
                }

                var options = {
                    series: series,
                    chart: {
                        height: 420,
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
                            speed: 600
                        },
                        fontFamily: 'inherit'
                    },
                    colors: colors,
                    stroke: {
                        width: [3.5, 2.5, 3.5],
                        curve: 'smooth',
                        dashArray: strokeDash
                    },
                    markers: {
                        size: [5, 4, 5],
                        strokeWidth: 2,
                        hover: {
                            size: 7
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function(val) {
                            return val ? Math.round(val) + '' : '';
                        },
                        offsetY: -7,
                        style: {
                            fontSize: '11px',
                            fontWeight: 600
                        },
                        background: {
                            enabled: true,
                            padding: 3,
                            borderRadius: 4,
                            opacity: 0.85
                        }
                    },
                    xaxis: {
                        categories: categories,
                        labels: {
                            style: {
                                fontSize: '11.5px',
                                fontWeight: 600
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
                                fontSize: '12px',
                                fontWeight: 600
                            }
                        },
                        labels: {
                            formatter: function(val) {
                                return Math.round(val) + ' cm';
                            }
                        }
                    },
                    grid: {
                        borderColor: '#f1f5f9',
                        strokeDashArray: 3
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
                        ? '<span class="badge bg-success-lt text-success px-2 py-1"><i class="ti ti-pin me-1"></i>Hiện tại (' + p.age + 't)</span>' 
                        : '<strong>' + p.age + ' tuổi</strong>';
                    var isCurrentClass = p.is_current ? 'is-current-row' : '';

                    // Mức tăng/năm
                    var growthDelta = '--';
                    if (prevPred !== null && !p.is_current) {
                        var diff = (p.height - prevPred).toFixed(1);
                        growthDelta = '<span class="text-orange fw-bold">+' + diff + ' cm</span>';
                    } else if (p.is_current) {
                        growthDelta = '<span class="text-muted fs-11">Điểm xuất phát</span>';
                    }
                    prevPred = p.height;

                    // So sánh chuẩn WHO
                    var whoDiffText = '--';
                    if (w !== null) {
                        var dw = (p.height - w).toFixed(1);
                        if (dw > 0) {
                            whoDiffText = '<span class="badge bg-green-lt text-green">+' + dw + ' cm vs WHO</span>';
                        } else if (dw < 0) {
                            whoDiffText = '<span class="badge bg-orange-lt text-orange">' + dw + ' cm vs WHO</span>';
                        } else {
                            whoDiffText = '<span class="badge bg-blue-lt text-blue">Đạt chuẩn WHO</span>';
                        }
                    }

                    var targetText = t ? '<strong class="text-danger">' + t.toFixed(1) + ' cm</strong>' : '<span class="text-muted">--</span>';

                    html += '<tr class="' + isCurrentClass + '">' +
                        '<td>' + ageLabel + '</td>' +
                        '<td class="text-orange fw-bold">' + p.height.toFixed(1) + ' cm</td>' +
                        '<td>' + growthDelta + '</td>' +
                        '<td class="text-info fw-bold">' + (w ? w.toFixed(1) + ' cm' : '--') + '</td>' +
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

            // 8. Print / Xuất Phác Đồ handler
            $(document).on('click', '#btn-print-phac-do', function() {
                window.print();
            });
        @endif
    });
</script>
