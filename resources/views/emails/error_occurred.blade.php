<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fixit Error Notification</title>
    <style>
        body {
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f9fafb;
            color: #111827;
            padding: 0;
            margin: 0;
        }

        .header {
            background-color: #1f2937;
            padding: 1.5rem 2rem;
            text-align: center;
        }

        .header h1 {
            color: #3b82f6;
            font-size: 1.75rem;
            margin: 0;
            font-weight: bold;
        }

        .container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .section {
            margin-top: 2rem;
        }

        .section h2 {
            color: #1f2937;
            font-size: 1.25rem;
            margin-bottom: 0.75rem;
            border-left: 4px solid #3b82f6;
            padding-left: 0.75rem;
        }

        .highlight {
            background-color: #f3f4f6;
            color: #1f2937;
            padding: 1rem;
            border-radius: 6px;
            font-size: 1rem;
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
            border-bottom: 1px solid #e5e7eb;
        }

        .details-table th {
            background-color: #f9fafb;
            color: #1f2937;
            font-weight: 600;
            width: 30%;
        }

        .details-table td {
            color: #4b5563;
        }

        .code-block {
            background-color: #f3f4f6;
            padding: 1rem;
            border-radius: 6px;
            font-family: monospace;
            font-size: 0.95rem;
            white-space: pre-wrap;
            overflow-x: auto;
            color: #1f2937;
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