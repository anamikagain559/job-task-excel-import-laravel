<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Users</title>
</head>
<body>

    <h1>Import Users</h1>

    <!-- Success Message -->
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <!-- Upload form -->
    <form action="{{ route('import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" required>
        <button type="submit">Import</button>
    </form>

    <!-- Show download link if failed rows exist -->
    @if(session('failed_file'))
        <div class="alert alert-warning">
            Import completed with some errors. You can <a href="{{ asset('storage/' . session('failed_file')) }}" class="btn btn-danger">download the failed rows</a> for review.
        </div>
    @endif

    <!-- Display Imported Data -->
  
    <h2>Imported Data</h2>
    <table border="1">
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


</body>
</html>
