<!DOCTYPE html>
<html>
<head>
    <title>Edit Data</title>
</head>
<body>
    <center>
        <h1>Edit Data</h1>

        <form action="/update/{{ $dok->id }}" method="POST" enctype="multipart/form-data">
            @csrf
            <table>
                <tr>
                    <th>Medicine:</th>
                    <td><input type="text" name="nama" value="{{ $dok->nama }}"></td>
                </tr>
                <tr>
                    <th>Patient:</th>
                    <td><input type="text" name="MC" value="{{ $dok->MC ?? '' }}"></td>
                </tr>
                <tr>
                    <th>Caretaker:</th>
                    <td><input type="text" name="ML" value="{{ $dok->ML ?? '' }}"></td>
                </tr>
                <tr>
                    <th>Type:</th>
                    <td><input type="text" name="Website" value="{{ $dok->Website ?? '' }}"></td>
                </tr>
                <tr>
                    <th>Patient's Rating:</th>
                    <td><input type="number" name="personal_rating" value="{{ $dok->personal_rating ?? '' }}" min="0" max="100"></td>
                </tr>
                <tr>
                    <th>Amount:</th>
                    <td><input type="number" name="Safeness" value="{{ $dok->Safeness ?? '' }}" min="0" max="100"></td>
                </tr>
                <tr>
                    <!-- <th>Current Photo:</th>
                    <td>
                        @if($dok->foto)
                            <img src="{{ asset('storage/' . $dok->foto) }}" width="100"><br>
                        @else
                            No photo uploaded<br>
                        @endif
                        <input type="file" name="foto">
                    </td> -->
                </tr>
            </table>

            <br>
            <button type="submit">Update</button>
        </form>
    </center>
</body>
</html>
