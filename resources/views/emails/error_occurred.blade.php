<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Application Error Notification</title>
    <style>
        body {
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #1e1e1e;
            color: #e5e7eb;
            padding: 2rem;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #2c2c2c;
            border-radius: 8px;
            padding: 2rem;
            border: 1px solid #3f3f3f;
        }

        h2 {
            font-size: 1.75rem;
            margin-bottom: 1.5rem;
            color: #f9fafb;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            background-color: #1e1e1e;
        }

        th, td {
            text-align: left;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #3f3f3f;
            vertical-align: top;
        }

        th {
            background-color: #111827;
            color: #93c5fd;
            font-weight: 600;
        }

        .section {
            margin-top: 2rem;
        }

        .code-block {
            background-color: #111827;
            padding: 1rem;
            border-radius: 6px;
            font-family: monospace;
            font-size: 0.95rem;
            white-space: pre-wrap;
            overflow-x: auto;
            color: #d1d5db;
        }

        .ai-label {
            font-size: 1.2rem;
            color: #34d399;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>⚠️ Exception Logged</h2>

        <table>
            <tr>
                <th>Message</th>
                <td>{{ $errorMessage }}</td>
            </tr>
        </table>

        @if($suggestion)
            <div class="section">
                <div class="ai-label">🧠 AI Suggestion</div>
                <div class="code-block">
                    {{ $suggestion }}
                </div>
            </div>
        @endif
    </div>
</body>
</html>
