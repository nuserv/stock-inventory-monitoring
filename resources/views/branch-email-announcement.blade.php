<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Branch Email Announcement</title>
    <style>
        body { max-width: 900px; margin: 30px auto; padding: 0 20px; font-family: Arial, sans-serif; color: #222; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 9px; border: 1px solid #ccc; text-align: left; }
        th { background: #f1f1f1; }
        .message { padding: 12px; margin-bottom: 18px; border-radius: 4px; }
        .success { color: #155724; background: #d4edda; }
        .error { color: #721c24; background: #f8d7da; }
        .pending { color: #856404; background: #fff3cd; }
        .preview { padding: 15px; border: 1px solid #ccc; background: #fafafa; }
        button { padding: 10px 18px; color: #fff; background: #1967d2; border: 0; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    <h1>Branch Email Announcement</h1>

    <div id="sendStatus" class="message" style="display: none;"></div>

    <p>
        <strong>To:</strong> jolopez@ideaserv.com.ph<br>
        <strong>BCC:</strong> {{ $recipients->count() }} operational branch email addresses
    </p>

    <h2>Message preview</h2>
    <div class="preview">
        @include('emails.branch-dr-ddr-announcement')
    </div>

    <h2>BCC recipients</h2>
    <table>
        <thead>
            <tr>
                <th>Branch</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($recipients as $recipient)
                <tr>
                    <td>{{ $recipient['branch'] }}</td>
                    <td>{{ $recipient['email'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <button type="button" id="sendAnnouncement">Send announcement</button>

    <script>
        (function () {
            var button = document.getElementById('sendAnnouncement');
            var status = document.getElementById('sendStatus');
            var statusUrl = window.location.pathname.replace(/\/+$/, '') + '/status';
            var initialStatus = @json($announcement ? $announcement->status : 'ready');
            var pollTimer;

            function showStatus(data) {
                status.style.display = 'block';
                status.textContent = data.message;

                if (data.total_batches) {
                    status.textContent += ' (' + data.completed_batches + '/' +
                        data.total_batches + ' batches completed)';
                }

                if (data.status === 'sent') {
                    status.className = 'message success';
                    button.disabled = true;
                    button.textContent = 'Announcement sent';
                    clearInterval(pollTimer);
                    pollTimer = null;
                    return;
                }

                if (data.status === 'failed') {
                    status.className = 'message error';
                    button.disabled = false;
                    button.textContent = 'Retry failed batches';
                    clearInterval(pollTimer);
                    pollTimer = null;
                    return;
                }

                if (data.status === 'queued' || data.status === 'sending') {
                    status.className = 'message pending';
                    button.disabled = true;
                    button.textContent = data.status === 'queued' ? 'Queued' : 'Sending...';
                    startPolling();
                    return;
                }

                status.style.display = 'none';
                button.disabled = false;
                button.textContent = 'Send announcement';
            }

            function readJson(response) {
                return response.json().catch(function () {
                    throw new Error('The server returned an invalid response.');
                }).then(function (data) {
                    if (!response.ok) {
                        throw new Error(data.message || 'Unable to send the announcement.');
                    }
                    return data;
                });
            }

            function refreshStatus() {
                return fetch(statusUrl, {
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }).then(readJson).then(showStatus);
            }

            function startPolling() {
                if (!pollTimer) {
                    pollTimer = setInterval(function () {
                        refreshStatus().catch(function (error) {
                            status.className = 'message error';
                            status.textContent = error.message;
                        });
                    }, 5000);
                }
            }

            button.addEventListener('click', function () {
                button.disabled = true;
                button.textContent = 'Queuing...';
                status.className = 'message pending';
                status.style.display = 'block';
                status.textContent = 'Queuing announcement...';

                fetch(window.location.pathname, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(readJson)
                .then(showStatus)
                .catch(function (error) {
                    status.className = 'message error';
                    status.textContent = error.message;
                    button.disabled = false;
                    button.textContent = 'Send announcement';
                });
            });

            if (initialStatus !== 'ready') {
                refreshStatus().catch(function (error) {
                    status.className = 'message error';
                    status.style.display = 'block';
                    status.textContent = error.message;
                    button.disabled = false;
                });
            }
        }());
    </script>
</body>
</html>
