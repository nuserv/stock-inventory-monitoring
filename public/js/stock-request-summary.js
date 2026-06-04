(function ($) {
    var activeStatus = '';
    var loadingTimer = null;

    function escapeRegex(value) {
        return value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    function getRequestTable() {
        if (!$.fn.dataTable.isDataTable('table.requestTable')) {
            return null;
        }

        return $('table.requestTable').DataTable();
    }

    function getStatusColumnIndex(dataTable) {
        var statusIndex = null;

        dataTable.columns().every(function (index) {
            var headerText = $(this.header()).text().trim().toUpperCase();

            if (headerText == 'STATUS') {
                statusIndex = index;
            }
        });

        return statusIndex;
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

    function applyStatusFilter(status, $item) {
        var dataTable = getRequestTable();
        var statusColumnIndex;

        if (!dataTable) {
            return;
        }

        showPageLoading();
        statusColumnIndex = getStatusColumnIndex(dataTable);

        if (statusColumnIndex === null) {
            clearPageLoading();
            return;
        }

        if (activeStatus == status) {
            activeStatus = '';
            dataTable.column(statusColumnIndex).search('', true, false);
        } else {
            activeStatus = status;
            dataTable.column(statusColumnIndex).search('^' + escapeRegex(status) + '$', true, false);
        }

        $('#stockRequestSummary .stock-request-summary__item').removeClass('is-active');

        if (activeStatus) {
            $('#stockRequestSummary .stock-request-summary__item').filter(function () {
                return $(this).data('status') == activeStatus;
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
                    var status = $item.data('status');
                    var count = data && data[status] ? data[status] : 0;

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
            applyStatusFilter($(this).data('status'), $(this));
        });

        $('#stockRequestSummary').on('keydown', '.stock-request-summary__item', function (event) {
            if (event.key == 'Enter' || event.key == ' ') {
                event.preventDefault();
                applyStatusFilter($(this).data('status'), $(this));
            }
        });

        $('table.requestTable').on('xhr.dt', function () {
            refreshStockRequestSummary();
        });

        $('table.requestTable').on('draw.dt', function () {
            clearPageLoading();
        });

        refreshStockRequestSummary();
    });
})(jQuery);
