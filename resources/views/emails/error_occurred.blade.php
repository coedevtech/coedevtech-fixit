<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Error Notification</title>
</head>
<body>
    <h2>An exception was logged</h2>

    <p><strong>Message:</strong></p>
    <pre>{{ $errorMessage }}</pre>

    @if($suggestion)
        <hr>
        <h3>🧠 AI Suggestion</h3>
        <pre>{{ $suggestion }}</pre>
    @endif
</body>
</html>
