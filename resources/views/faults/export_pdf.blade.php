@php($title = 'Faults Export')
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #111; }
        h1 { font-size: 18px; margin-bottom: 8px; }
        .meta { font-size: 11px; color: #666; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; }
        th { background: #f0f0f0; text-align: left; }
        tbody tr:nth-child(even) { background: #fafafa; }
    </style>
    </head>
<body>
    <h1>{{ $title }}</h1>
    <div class="meta">Generated at: {{ $generatedAt }}</div>
    <table>
        <thead>
            <tr>
                <th>Ref No</th>
                <th>Customer</th>
                <th>Account Manager</th>
                <th>Link</th>
                <th>Assigned To</th>
                <th>Date Reported</th>
                <th>Logged By</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($faults as $f)
            <tr>
                <td>{{ $f->fault_ref_number }}</td>
                <td>{{ $f->customer }}</td>
                <td>{{ $f->accountManager }}</td>
                <td>{{ $f->link }}</td>
                <td>{{ $f->assignedTo }}</td>
                <td>{{ \Carbon\Carbon::parse($f->created_at)->format('Y-m-d H:i') }}</td>
                <td>{{ $f->reportedBy }}</td>
                <td>{{ $f->description }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>