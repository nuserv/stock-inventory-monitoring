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
        .preview { padding: 15px; border: 1px solid #ccc; background: #fafafa; }
        button { padding: 10px 18px; color: #fff; background: #1967d2; border: 0; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    <h1>Branch Email Announcement</h1>

    @if (session('announcement_success'))
        <div class="message success">{{ session('announcement_success') }}</div>
    @endif

    @if (session('announcement_error'))
        <div class="message error">{{ session('announcement_error') }}</div>
    @endif

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

    <form method="POST" action="{{ route('branch-email-announcement.send') }}"
          onsubmit="return confirm('Send this announcement to all listed branch emails?');">
        @csrf
        <button type="submit">Send announcement</button>
    </form>
</body>
</html>
