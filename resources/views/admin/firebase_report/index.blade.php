@extends('admin.layouts.master')

@section('content')
    <style>
        .page-body {
            background: #F8FAFC;
            min-height: 100vh;
            padding: 2rem 0;
        }

        .report-title {
            font-size: 1.75rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: #1a1f36;
        }

        .custom-shadow {
            border: none;
            border-radius: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .card-header {
            background: transparent !important;
            border-bottom: 1px solid #E5E7EB;
            padding: 1.5rem;
        }

        /* Modern Segmented Filter Pills */
        .filter-pill-container {
            background: #E2E8F0;
            padding: 4px;
            border-radius: 30px;
            display: inline-flex;
            align-items: center;
            gap: 2px;
        }

        .btn-filter-pill {
            background: transparent;
            border: none;
            color: #475569;
            font-size: 0.8rem;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 30px;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
        }

        .btn-filter-pill:hover {
            color: #0f172a;
        }

        .btn-filter-pill.active {
            background: #FFFFFF;
            color: #206bc4;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1), 0 1px 2px rgba(0,0,0,0.06);
        }

        .btn-filter-pill.disabled {
            opacity: 0.6;
            pointer-events: none;
        }
    </style>

    <div class="page-body">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                     <div class="card custom-shadow">
                        <div class="card-header bg-white border-0 pt-4 pb-2">
                            <div class="d-flex align-items-center justify-content-between">
                                <h2 class="report-title mb-0">
                                    <i class="ti ti-chart-bar text-primary me-2"></i>Thống kê Firebase
                                </h2>
                                @if(!$analyticsError)
                                    <span class="badge bg-success-lt">
                                        <i class="ti ti-check me-1"></i>Dữ liệu trực tiếp (Live)
                                    </span>
                                @else
                                    <span class="badge bg-danger-lt">
                                        <i class="ti ti-alert-triangle me-1"></i>Có lỗi kết nối
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Chart: Active Users Over Time -->
                                <div class="col-12 mb-4">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body">
                                            @if($analyticsError)
                                                <div class="alert alert-important alert-danger mb-0" role="alert">
                                                    <div class="d-flex">
                                                        <div>
                                                            <i class="ti ti-alert-triangle icon alert-icon fs-2 me-2"></i>
                                                        </div>
                                                        <div>
                                                            <h4 class="alert-title font-weight-bold mb-1">Lỗi kết nối Google Analytics API</h4>
                                                            <div class="text-white opacity-75">{{ $analyticsError }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
                                                    <div class="d-flex flex-column flex-sm-row align-items-sm-center gap-2">
                                                        <h4 class="card-title font-weight-semibold mb-0" id="chart-active-users-title">Người dùng hoạt động (30 ngày gần nhất)</h4>
                                                        <span class="badge bg-blue-lt px-3 py-2 rounded-pill font-weight-bold" style="font-size: 0.85rem;" id="chart-active-users-total">
                                                            Tổng: {{ number_format(array_sum(array_column($activeUsersOverTime, 'users'))) }} lượt
                                                        </span>
                                                    </div>
                                                    <div class="filter-pill-container">
                                                        <button type="button" class="btn-filter-pill btn-period" data-period="1d">1 Ngày</button>
                                                        <button type="button" class="btn-filter-pill btn-period" data-period="7d">1 Tuần</button>
                                                        <button type="button" class="btn-filter-pill btn-period active" data-period="30d">1 Tháng</button>
                                                    </div>
                                                </div>
                                                <div class="position-relative">
                                                    <div id="chart-active-users" style="width: 100%; height: 350px;"></div>
                                                    <div id="chart-loading" class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center d-none" style="background: rgba(255, 255, 255, 0.7); z-index: 10; border-radius: 4px;">
                                                        <div class="spinner-border text-primary" role="status"></div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('libs-js')
        <!-- amCharts 5 Resources -->
        <script src="https://cdn.amcharts.com/lib/5/index.js"></script>
        <script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
        <script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>
    @endpush

    @push('custom-js')
    <script>
        var activeUsersXAxis;
        var activeUsersSeries;

        @if(!$analyticsError)
        // 1. Active Users Over Time
        am5.ready(function() {
            var root = am5.Root.new("chart-active-users");
            root._logo.dispose();
            root.setThemes([am5themes_Animated.new(root)]);

            var chart = root.container.children.push(am5xy.XYChart.new(root, {
                panX: true,
                panY: true,
                wheelX: "panX",
                wheelY: "zoomX",
                pinchZoomX: true
            }));

            var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {
                behavior: "none"
            }));
            cursor.lineY.set("visible", false);

            var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
                categoryField: "date",
                renderer: am5xy.AxisRendererX.new(root, { minGridDistance: 50 }),
                tooltip: am5.Tooltip.new(root, {})
            }));

            var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
                renderer: am5xy.AxisRendererY.new(root, {})
            }));

            var series = chart.series.push(am5xy.LineSeries.new(root, {
                name: "Người dùng",
                xAxis: xAxis,
                yAxis: yAxis,
                valueYField: "users",
                categoryXField: "date",
                tooltip: am5.Tooltip.new(root, {
                    labelText: "{valueY} người dùng"
                })
            }));

            series.strokes.template.setAll({ strokeWidth: 3 });
            
            series.fills.template.setAll({
                visible: true,
                fillOpacity: 0.2
            });

            var data = {!! json_encode($activeUsersOverTime) !!};
            xAxis.data.setAll(data);
            series.data.setAll(data);

            activeUsersXAxis = xAxis;
            activeUsersSeries = series;

            series.appear(1000);
            chart.appear(1000, 100);
        });

        // Period Filtering Handler
        $(document).ready(function() {
            $('.btn-period').on('click', function() {
                var btn = $(this);
                var period = btn.data('period');

                if (btn.hasClass('active') || btn.hasClass('disabled')) {
                    return;
                }

                $('.btn-period').addClass('disabled');
                $('#chart-loading').removeClass('d-none'); // Show loading spinner

                $.ajax({
                    url: "{{ route('admin.firebase.report') }}",
                    type: 'GET',
                    data: { period: period },
                    success: function(response) {
                        $('.btn-period').removeClass('active');
                        btn.addClass('active');

                        if (activeUsersXAxis && activeUsersSeries) {
                            activeUsersXAxis.data.setAll(response.activeUsers);
                            activeUsersSeries.data.setAll(response.activeUsers);
                        }

                        // Calculate and update total active users
                        var totalUsers = 0;
                        if (response.activeUsers && response.activeUsers.length > 0) {
                            totalUsers = response.activeUsers.reduce(function(sum, item) {
                                return sum + parseInt(item.users || 0);
                            }, 0);
                        }
                        $('#chart-active-users-total').text('Tổng: ' + totalUsers.toLocaleString('vi-VN') + ' lượt');

                        var titleText = 'Người dùng hoạt động (30 ngày gần nhất)';
                        if (period === '1d') {
                            titleText = 'Người dùng hoạt động (24 giờ gần nhất)';
                        } else if (period === '7d') {
                            titleText = 'Người dùng hoạt động (7 ngày gần nhất)';
                        }
                        $('#chart-active-users-title').text(titleText);
                    },
                    error: function(xhr, status, error) {
                        console.error('Lỗi khi tải dữ liệu thống kê:', error);
                        var errMsg = 'Không thể tải dữ liệu thống kê. Vui lòng thử lại sau.';
                        if (xhr.responseJSON && xhr.responseJSON.error) {
                            errMsg = xhr.responseJSON.error;
                        }
                        alert('Lỗi: ' + errMsg);
                    },
                    complete: function() {
                        $('.btn-period').removeClass('disabled');
                        $('#chart-loading').addClass('d-none'); // Hide loading spinner
                    }
                });
            });
        });
        @endif


    </script>
    @endpush
@endsection
