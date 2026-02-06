<!DOCTYPE html>
<html>
<head>
    <title>Edit Employee</title>
</head>
<body>
<center>
    <h1>Edit Employee Information</h1>

    <form action="/EmployUpdate/{{ $emp->employeeid }}" method="POST">
        @csrf
        <table>
            <tr><th>Name:</th><td><input type="text" name="employeename" value="{{ $emp->employeename }}" required></td></tr>
            <tr><th>Age:</th><td><input type="number" name="employeeage" value="{{ $emp->employeeage }}" required></td></tr>
            <tr><th>Gender:</th>
                <td>
                    <select name="employeegender" required>
                        <option value="Male" {{ $emp->employeegender == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ $emp->employeegender == 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ $emp->employeegender == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </td>
            </tr>
            <tr><th>Birthdate:</th><td><input type="date" name="employeebirthdate" value="{{ $emp->employeebirthdate }}"></td></tr>
            <tr><th>Race:</th><td><input type="text" name="employeerace" value="{{ $emp->employeerace }}"></td></tr>
            <tr><th>Religion:</th><td><input type="text" name="employeereligion" value="{{ $emp->employeereligion }}"></td></tr>
            <tr><th>Blood Type:</th><td><input type="text" name="employeebloodtype" value="{{ $emp->employeebloodtype }}"></td></tr>
            <tr><th>Position:</th><td><input type="text" name="employeeposition" value="{{ $emp->employeeposition }}" required></td></tr>

            <tr><th>Linked Username:</th><td><input type="text" name="username" value="{{ $emp->username }}"></td></tr>
            <tr><th>User Level:</th><td><input type="text" name="level" value="{{ $emp->level }}"></td></tr>
            <input type="hidden" name="userid" value="{{ $emp->userid ?? '' }}">
        </table>
        <br>
        <button type="submit">Save Changes</button>
    </form>
</center>
</body>
</html>
