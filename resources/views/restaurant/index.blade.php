<!DOCTYPE html>
<html>
<head>
    <title>Restaurant List</title>
    <style>
        table {
            width: 80%;
            margin: 40px auto;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background: #eee;
        }
    </style>
</head>
<body>

<h2 style="text-align:center;">Restaurants</h2>

<table>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Location</th>
        <th>Phone</th>
    </tr>

    @foreach ($restaurants as $restaurant)
        <tr>
            <td>{{ $restaurant->id }}</td>
            <td>{{ $restaurant->name }}</td>
            <td>{{ $restaurant->location }}</td>
            <td>{{ $restaurant->phone }}</td>
        </tr>
    @endforeach

</table>

</body>
</html>
