<!DOCTYPE html>
<html>
<head>
    <title>Add New Data</title>
</head>
<body>
    <center>
        <h1>Add New Data</h1>
                  @if(session('level') == 2 || session('level') == 3)


        <form action="/simpan" method="POST" enctype="multipart/form-data">
            @csrf
            <table>
                <tr>
    <th>Title:</th>
    <td><input type="text" name="Title" required></td>
</tr>

                <tr>
                    <th>Medicine:</th>
                    <td><input type="text" name="nama" required></td>
                </tr>
                <tr>
                    <th>Patient:</th>
                    <td><input type="text" name="MC"></td>
                </tr>
                <tr>
                    <th>Caretaker:</th>
                    <td><input type="text" name="ML"></td>
                </tr>
                <tr>
                    <th>Type:</th>
                    <td><input type="text" name="Website"></td>
                </tr>
                <tr>
                    <th>Patient's Rating:</th>
                    <td><input type="number" name="personal_rating" min="0" max="100"></td>
                </tr>
                <tr>
                    <th>Amount:</th>
                    <td><input type="number" name="Safeness" min="0" max="100"></td>
                </tr>
                <!-- <tr>
                    <th>Photo:</th>
                    <td><input type="file" name="foto"></td>
                </tr> -->
            </table>
            <br>
            <center> <button type="submit" class="btn btn-outline-dark">Add Medicine</button> </center>
            @endif
        </form>
    </center>
</body>
</html>
