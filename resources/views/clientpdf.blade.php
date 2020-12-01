<!DOCTYPE html>
<html lang="fr">

<head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>

<body>
    <div class="container">


        <h1>Liste des Clients</h1>
        <h2>{{ $clients[0]->event }}</h2>

        <table>
            <thead>
                <tr class="table-success">
                    <th scope="col">Code Pass</th>
                    <th scope="col">Nom Client</th>
                    <th scope="col">Téléphone Client</th>
                    <th scope="col">Nom Package</th>
                    <th scope="col">Nom Vendeur</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($clients as $client)
                    <tr>
                        <td>{{ $client->codePass }}</td>
                        <td>{{ $client->nomClient }}</td>
                        <td>{{ $client->telClient }}</td>
                        <td>{{ $client->nomPack }}</td>
                        <td>{{ $client->nomVendeur }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>


</body>
<footer>
    <small>MyEvent @<?php echo date('Y'); ?> - {{ $clients[0]->event }}</small>
</footer>


</html>
