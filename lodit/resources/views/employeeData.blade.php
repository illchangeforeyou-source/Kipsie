<!DOCTYPE html>
<html>
<head>
    <title>Add New Employee + User</title>
</head>
<body>
<center>
    <h1>Add New Employee & User Account</h1>

    @if(session('level') == 2 || session('level') == 3)
    <form action="/saveEmployee" method="POST">
        @csrf
        <table>
            <tr><th colspan="2"><h3>User Login Info</h3></th></tr>
            <tr><th>Username:</th><td><input type="text" name="username" required></td></tr>
            <tr><th>Password:</th><td><input type="password" name="password" required></td></tr>
            <tr><th>User Level:</th>
                <td>
                    <select name="level" required>
                        <option value="">-- Select Level --</option>
                        <option value="1">Customer</option>
                        <option value="2">Manager</option>
                        <option value="3">Admin</option>
                        <option value="4">Super Admin</option>
                        <option value="5">Owner</option>
                        <option value="6">Pharmacist</option>
                    </select>
                </td>
            </tr>

            <tr><th colspan="2"><h3>Employee Information</h3></th></tr>
            <tr><th>Name:</th><td><input type="text" name="employeename" required></td></tr>
            <tr><th>Age:</th><td><input type="number" name="employeeage" min="18" required></td></tr>
            <tr><th>Gender:</th>
                <td>
                    <select name="employeegender" required>
                        <option value="">-- Select Gender --</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </td>
            </tr>
            <tr><th>Birthdate:</th><td><input type="date" name="employeebirthdate"></td></tr>
            <tr><th>Race:</th><td><input type="text" name="employeerace"></td></tr>
            <tr><th>Religion:</th><td><input type="text" name="employeereligion"></td></tr>
            <tr><th>Blood Type:</th><td><input type="text" name="employeebloodtype"></td></tr>
            <tr><th>Position:</th><td><input type="text" name="employeeposition" required></td></tr>
        </table>
        <br>
        <center> <button type="submit" class="btn btn-outline-dark">Add Employee</button> </center>
    </form>
    @else
        <p>You don’t have permission to access this page.</p>
    @endif
</center>
</body>
</html>
