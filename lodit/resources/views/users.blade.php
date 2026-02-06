@extends('layouts.app')

@section('title', 'LODIT - Employee Management')

@section('content')

<div class="table-responsive">
    <table class="table table-striped table-hover table-bordered align-middle">
    <thead class="table-secondary">
        <tr>
            <th>No.</th>
            <th>Name</th>
            <th>Age</th>
            <th>Gender</th>
            <th>Birthdate</th>
            <th>Race</th>
            <th>Religion</th>
            <th>Blood Type</th>
            <th>Position</th>
            <th>Username</th>
            <th>Level</th>
            <th>Role</th>
            @if(session('level') == 3)
                <th>Action</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach($joined as $value)
            @if(session('level') == 2 || session('level') == 3)
                <tr>
                    <td>{{ $value->employeeid }}</td>
                    <td>{{ $value->employeename }}</td>
                    <td>{{ $value->employeeage }}</td>
                    <td>{{ $value->employeegender }}</td>
                    <td>{{ $value->employeebirthdate }}</td>
                    <td>{{ $value->employeerace }}</td>
                    <td>{{ $value->employeereligion }}</td>
                    <td>{{ $value->employeebloodtype }}</td>
                    <td>{{ $value->employeeposition }}</td>
                    <td>{{ $value->username }}</td>
                    <td>{{ $value->level }}</td>
                    <td>{{ $value->beingas }}</td>
                    @if(session('level') == 3)
                        <td>
                            <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewModal{{ $value->employeeid }}">
                                View
                            </button>
                        </td>
                    @endif
                </tr>
            @endif
        @endforeach
    </tbody>
    </table>
</div>

@foreach($joined as $value)
<div class="modal fade" id="viewModal{{ $value->employeeid }}" tabindex="-1" aria-labelledby="viewLabel{{ $value->employeeid }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark">

            <div class="modal-header border-0">
                <h5 class="modal-title text-white">
                    Employee Details – {{ $value->employeename }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <table class="table" style="background-color:#2d2d2d; border-radius: 8px; overflow:hidden;">
                    <tr><th>Name</th><td>{{ $value->employeename }}</td></tr>
                    <tr><th>Age</th><td>{{ $value->employeeage }}</td></tr>
                    <tr><th>Gender</th><td>{{ $value->employeegender }}</td></tr>
                    <tr><th>Birthdate</th><td>{{ $value->employeebirthdate }}</td></tr>
                    <tr><th>Race</th><td>{{ $value->employeerace }}</td></tr>
                    <tr><th>Religion</th><td>{{ $value->employeereligion }}</td></tr>
                    <tr><th>Blood Type</th><td>{{ $value->employeebloodtype }}</td></tr>
                    <tr><th>Position</th><td>{{ $value->employeeposition }}</td></tr>
                    <tr><th>Username</th><td>{{ $value->username }}</td></tr>
                    <tr><th>Level</th><td>{{ $value->level }}</td></tr>
                    <tr><th>Role</th><td>{{ $value->beingas }}</td></tr>
                </table>

                <div class="text-center mt-3">
                    <button class="btn btn-primary mb-2" data-bs-toggle="modal" data-bs-target="#editModal{{ $value->employeeid }}" data-bs-dismiss="modal">Edit</button>
                    <button class="btn btn-danger mb-2" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $value->employeeid }}" data-bs-dismiss="modal">Delete</button>
                    <button class="btn btn-warning mb-2" data-bs-toggle="modal" data-bs-target="#resetModal{{ $value->userid }}" data-bs-dismiss="modal">Reset</button>
                </div>

            </div>

            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="editModal{{ $value->employeeid }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <form action="{{ url('/EmployEdit/'.$value->employeeid) }}" method="POST">
                @csrf
                 @method('PUT')
                <div class="modal-header border-0">
                    <h5 class="modal-title text-white">Edit Employee</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <input type="text" name="employeename" value="{{ $value->employeename }}" class="form-control mb-2" style="background:#333; color:white;">

                    <input type="number" name="employeeage" value="{{ $value->employeeage }}" class="form-control mb-2" style="background:#333; color:white;">

                    <input type="text" name="employeegender" value="{{ $value->employeegender }}" class="form-control mb-2" style="background:#333; color:white;">

                    <input type="date" name="employeebirthdate" value="{{ $value->employeebirthdate }}" class="form-control mb-2" style="background:#333; color:white;">

                    <input type="text" name="employeerace" value="{{ $value->employeerace }}" class="form-control mb-2" style="background:#333; color:white;">

                    <input type="text" name="employeereligion" value="{{ $value->employeereligion }}" class="form-control mb-2" style="background:#333; color:white;">

                    <input type="text" name="employeebloodtype" value="{{ $value->employeebloodtype }}" class="form-control mb-2" style="background:#333; color:white;">

                    <input type="text" name="employeeposition" value="{{ $value->employeeposition }}" class="form-control mb-2" style="background:#333; color:white;">

                    <input type="text" name="username" value="{{ $value->username }}" class="form-control mb-2" style="background:#333; color:white;">

                    <input type="number" name="level" value="{{ $value->level }}" class="form-control mb-2" style="background:#333; color:white;">

                    <input type="text" name="beingas" value="{{ $value->beingas }}" class="form-control mb-2" style="background:#333; color:white;">

                </div>

                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>

            </form>
        </div>
    </div>
</div>



<!-- Delete Modal --><div class="modal fade" id="deleteModal{{ $value->employeeid }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white text-center">

            <div class="modal-body">
                Are you sure you want to delete <strong>{{ $value->employeename }}</strong>?
            </div>

            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="{{ url('/delEmployee/'.$value->employeeid) }}" class="btn btn-danger">Delete</a>
            </div>

        </div>
    </div>
</div>

<!-- Reset Modal --><div class="modal fade" id="resetModal{{ $value->userid }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-center text-white">

            <div class="modal-body">
                Reset password for <strong>{{ $value->username }}</strong> to default?
            </div>

            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="{{ url('/resetPassword/'.$value->userid) }}" class="btn btn-warning">Reset</a>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="addEmployeeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-light">

            <form action="/addEmployee" method="POST">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Add Employee</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" required>

                    <label class="mt-2">Email</label>
                    <input type="email" name="email" class="form-control" required>

                    <label class="mt-2">Password</label>
                    <input type="password" name="password" class="form-control" required>

                    <label class="mt-2">Role</label>
                    <select name="role" class="form-control">
                        <option value="1">Normal</option>
                        <option value="2">Pharmacist</option>
                        <option value="3">Admin</option>
                    </select>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-success" type="submit">Add Employee</button>
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>

            </form>

        </div>
    </div>
</div>


@endforeach

@endsection