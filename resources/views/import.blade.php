<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Users</title>
    <style>
        .alert {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }
        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
    </style>
</head>
<body>

    <h1>Import Users</h1>
 
    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Upload Form -->
    <form action="{{ route('import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" required>
        <button type="submit">Import</button>
    </form>

    <!-- Show download link if failed rows exist -->
    @if(session('failed_file'))
        <div class="alert alert-warning">
            {{ session('successRows') ?? 0 }} rows imported successfully and {{ session('totalFaileds') ?? 0 }} rows contain errors. 
            You can <a href="{{ asset('storage/' . session('failed_file')) }}" class="btn btn-danger">download the failed rows</a> for review.
        </div>
    @endif

    <!-- Imported Data Table -->
    @if(isset($importedData) && $importedData->count() > 0)
        <h2>Imported Data</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Gender</th>
                </tr>
            </thead>
            <tbody>
                @foreach($importedData as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone }}</td>
                        <td>{{ $user->gender }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>No data has been imported yet.</p>
    @endif

</body>
</html>
