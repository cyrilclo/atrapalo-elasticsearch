<?php
$firstNames = [];
$lastNames = [];
$sportsList = [];
$footballTeams = [];

if (($handle = fopen("data/first_names.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle)) !== FALSE) {
        $firstNames[] = $data[0];
    }
    fclose($handle);
}

if (($handle = fopen("data/last_names.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle)) !== FALSE) {
        foreach ($data as $caca) {
            $lastNames[] = $caca;
        }
    }
    fclose($handle);
}

if (($handle = fopen("data/sports.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle)) !== FALSE) {
        $sportsList[] = $data[0];
    }
    fclose($handle);
}

if (($handle = fopen("data/football_clubs.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle)) !== FALSE) {
        $footballTeams[] = $data[0];
    }
    fclose($handle);
}

echo "Importing persons in ES...";
for($i = 1; $i <= 300; $i++) {

    $firstName = $firstNames[array_rand($firstNames, 1)];
    $lastName = $lastNames[array_rand($lastNames, 1)];
    $sportsIds = array_rand($sportsList, rand ( 1 , 6 ));

    $sports = [];
    if (is_array($sportsIds)) {
        foreach ($sportsIds as $sportId) {
            $sports[] = $sportsList[$sportId];
        }
    }
    else {
        $sports[] = $sportsList[$sportsIds];
    }
    $favoriteTeam = $footballTeams[array_rand($footballTeams, 1)];
    $email = strtolower($firstName . ' ' . $lastName . ' ' . rand(1, 60) . '@gmail.com');
    $age = rand ( 15 , 80 );
    $email = str_replace(' ', '_', $email);
    $sportsText = implode(', ', $sports);

    $description = "My name is {$firstName} {$lastName}, I'm {$age}, I love practice and watch {$sportsText} and I'm a big fan of {$favoriteTeam} football team";

    $creation_date_random_timestamp = mt_rand(1483277990,time());


    $data = array(
        "first_name" => $firstName,
        "last_name" => $lastName,
        "email" => $email,
        "favorite_football_club" => $favoriteTeam,
        "age" => $age,
        "description" => $description,
        "created_at" => date("Y-m-d H:i:s",$creation_date_random_timestamp),
        "sports" => $sports,
    );
    $data_string = json_encode($data);

    $ch = curl_init('http://localhost:9200/person/doc/');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string))
    );

    $result = curl_exec($ch);
}

echo "DONE!";
