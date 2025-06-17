<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fixit Error Notification</title>
    <style>
        body {
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #1e1e1e;
            color: #e5e7eb;
            padding: 0;
            margin: 0;
        }

        .header {
            background-color: #000000;
            padding: 1.5rem 2rem;
            text-align: center;
        }

        .header h1 {
            color: #38bdf8;
            font-size: 1.75rem;
            margin: 0;
            font-weight: bold;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
            background-color: #2c2c2c;
        }

        .section {
            margin-top: 2rem;
        }

        .section h2 {
            color: #f9fafb;
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
            border-bottom: 1px solid #3f3f3f;
            padding-bottom: 0.25rem;
        }

        .highlight {
            background-color: #111827;
            color: #fca5a5;
            padding: 1rem;
            border-radius: 6px;
            font-size: 0.95rem;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        .details-table {
            width: 100%;
            margin-top: 1rem;
            border-collapse: collapse;
        }

        .details-table th,
        .details-table td {
            text-align: left;
            padding: 0.75rem;
            border-bottom: 1px solid #3f3f3f;
        }

        .details-table th {
            background-color: #111827;
            color: #93c5fd;
            font-weight: 600;
            width: 30%;
        }

        .details-table td {
            color: #d1d5db;
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
    </style>
</head>
<body>
    <div class="header">
        <h1>⚙️ Fixit</h1>
    </div>

    <div class="container">
        <!-- Section: Error Message -->
        <div class="section">
            <h2>🚨 Error Message</h2>
            <div class="highlight">
                {{ $errorMessage }}
            </div>
        </div>

        <!-- Section: Details -->
        <div class="section">
            <h2>📅 Error Details</h2>
            <table class="details-table">
                <tr>
                    <th>Date</th>
                    <td>{{ $date }}</td>
                </tr>
                <tr>
                    <th>Environment</th>
                    <td>{{ $environment }}</td>
                </tr>
                @isset($occurrences)
                    <tr>
                        <th>Occurrences</th>
                        <td>{{ $occurrences }}</td>
                    </tr>
                @endisset
            </table>
        </div>

        <!-- Section: Exception -->
        <div class="section">
            <h2>🧱 Exception Trace</h2>
            <div class="code-block">
                {{ $exception ?? 'No exception trace available.' }}
            </div>
        </div>

        <!-- Section: AI Suggestion -->
        @if (!empty($suggestion))
            <div class="section">
                <h2>🧠 AI Suggestion</h2>
                <div class="code-block">
                    {{ $suggestion }}
                </div>
            </div>
        @endif
    </div>
</body>
</html>
