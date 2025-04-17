@include('/partials.head')
<body class="bg-light">
    <div class="container py-4">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-header">
                <h1 class="h4 mb-0">Employee Details</h1>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>Name:</strong> {{ $employee->name }}
                </div>
                <div class="mb-3">
                    <strong>Email:</strong> {{ $employee->email }}
                </div>
                <div class="mb-3">
                    <strong>Message:</strong> {{ $employee->message }}
                </div>
                <div class="mb-3">
                    <strong>Sent:</strong> {{ $employee->sent ? 'Yes' : 'No' }}
                </div>
                <div>
                    <a href="{{ route('employees.edit', $employee) }}" class="btn btn-primary">Edit</a>
                    <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger"
                                onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                    <a href="{{ route('employees.index') }}" class="btn btn-secondary">Back</a>
                </div>
            </div>
        </div>
    </div>
    @include('partials.footer')
