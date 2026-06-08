(function ($) {
    var activeFilterKey = '';
    var loadingTimer = null;
    var resizeTimer = null;

    function escapeRegex(value) {
        return value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    function getRequestTable() {
        if (!$.fn.dataTable.isDataTable('table.requestTable')) {
            return null;
        }

        return $('table.requestTable').DataTable();
    }

    function adjustRequestTable() {
        var dataTable = getRequestTable();

        if (!dataTable) {
            return;
        }

        dataTable.columns.adjust();

        if (dataTable.responsive && dataTable.responsive.recalc) {
            dataTable.responsive.recalc();
        }
    }

    function queueRequestTableAdjust() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(adjustRequestTable, 150);
    }

    function clearRequestModalQueryState() {
        var url = new URL(window.location.href);

        if (!url.searchParams.has('reqno')) {
            return;
        }

        url.searchParams.delete('reqno');

        if (!url.searchParams.toString()) {
            window.history.replaceState({}, document.title, url.pathname);
            return;
        }

        window.history.replaceState({}, document.title, url.pathname + '?' + url.searchParams.toString());
    }

    function getColumnIndex(dataTable, columnName) {
        var columnIndex = null;

        dataTable.columns().every(function (index) {
            var headerText = $(this.header()).text().trim().toUpperCase();

            if (headerText == columnName) {
                columnIndex = index;
            }
        });

        return columnIndex;
    }

    function showPageLoading() {
        clearTimeout(loadingTimer);
        $('body').addClass('stock-request-filtering');
        $('#loading').show();
    }

    function clearPageLoading() {
        clearTimeout(loadingTimer);
        loadingTimer = setTimeout(function () {
            $('#loading').hide();
            $('body').removeClass('stock-request-filtering');
        }, 300);
    }

    function applyStatusFilter(status, requestType, filterKey) {
        var dataTable = getRequestTable();
        var statusColumnIndex;
        var typeColumnIndex;

        if (!dataTable) {
            return;
        }

        showPageLoading();
        statusColumnIndex = getColumnIndex(dataTable, 'STATUS');
        typeColumnIndex = getColumnIndex(dataTable, 'REQUEST TYPE');

        if (statusColumnIndex === null) {
            clearPageLoading();
            return;
        }

        if (activeFilterKey == filterKey) {
            activeFilterKey = '';
            dataTable.column(statusColumnIndex).search('', true, false);
            if (typeColumnIndex !== null) {
                dataTable.column(typeColumnIndex).search('', true, false);
            }
        } else {
            activeFilterKey = filterKey;
            dataTable.column(statusColumnIndex).search('^' + escapeRegex(status) + '$', true, false);
            if (typeColumnIndex !== null) {
                dataTable.column(typeColumnIndex).search(requestType ? '^' + escapeRegex(requestType) + '$' : '', true, false);
            }
        }

        $('#stockRequestSummary .stock-request-summary__item').removeClass('is-active');

        if (activeFilterKey) {
            $('#stockRequestSummary .stock-request-summary__item').filter(function () {
                return $(this).data('key') == activeFilterKey;
            }).addClass('is-active');
        }

        setTimeout(function () {
            dataTable.draw();
        }, 50);
    }

    function refreshStockRequestSummary() {
        var $summary = $('#stockRequestSummary');

        if (!$summary.length) {
            return;
        }

        $.ajax({
            type: 'get',
            url: $summary.data('summary-url'),
            success: function (data) {
                $summary.find('.stock-request-summary__item').each(function () {
                    var $item = $(this);
                    var key = $item.data('key');
                    var count = data && data[key] ? data[key] : 0;

                    $item.find('.stock-request-summary__count').text(count);
                });
            },
            error: function (data) {
                if (data.status == 401) {
                    window.location.href = '/login';
                }
            }
        });
    }

    $(document).ready(function () {
        showPageLoading();

        $('#stockRequestSummary').on('click', '.stock-request-summary__item', function () {
            applyStatusFilter($(this).data('status'), $(this).data('type'), $(this).data('key'));
        });

        $('#stockRequestSummary').on('keydown', '.stock-request-summary__item', function (event) {
            if (event.key == 'Enter' || event.key == ' ') {
                event.preventDefault();
                applyStatusFilter($(this).data('status'), $(this).data('type'), $(this).data('key'));
            }
        });

        $('table.requestTable').on('xhr.dt', function () {
            refreshStockRequestSummary();
        });

        $('table.requestTable').on('draw.dt', function () {
            queueRequestTableAdjust();
            clearPageLoading();
        });

        $(window).on('resize.stockRequestSummary orientationchange.stockRequestSummary', function () {
            queueRequestTableAdjust();
        });

        $('#requestModal').on('hidden.bs.modal', function () {
            clearRequestModalQueryState();
            queueRequestTableAdjust();
        });

        refreshStockRequestSummary();
    });
})(jQuery);
