<?php
$xmlFile = 'members.xml';

if (!file_exists($xmlFile)) {
    file_put_contents($xmlFile, '<members></members>');
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

function loadXML($file) {
    return simplexml_load_file($file);
}

function saveXML($xml, $file) {
    $xml->asXML($file);
}

switch ($action) {
    case 'list':
        $xml = loadXML($xmlFile);
        $members = [];
        foreach ($xml->member as $m) {
            $members[] = [
                'photo' => (string) $m->photo,
                'name' => (string) $m->name,
                'number' => (string) $m->number,
                'age' => (int) $m->age,
                'gender' => (string) $m->gender,
                'address' => (string) $m->address,
                'membership' => (string) $m->membership,
            ];
        }
        header('Content-Type: application/json');
        echo json_encode($members);
        break;

    case 'get':
        $id = (int) $_GET['id'];
        $xml = loadXML($xmlFile);
        if (isset($xml->member[$id])) {
            $m = $xml->member[$id];
            echo json_encode([
                'photo' => isset($m->photo) ? (string) $m->photo : '',
                'name' => (string) $m->name,
                'number' => (string) $m->number,
                'age' => (int) $m->age,
                'gender' => (string) $m->gender,
                'address' => (string) $m->address,
                'membership' => (string) $m->membership,
            ]);
        }
        break;

    case 'add':
        $xml = loadXML($xmlFile);
        $new = $xml->addChild('member');
        $filename = '';
        if ($_FILES['photo']['tmp_name']) {
            $filename = 'uploads/' . basename($_FILES['photo']['name']);
            move_uploaded_file($_FILES['photo']['tmp_name'], $filename);
        }
        $new->addChild('photo', $filename);
        $new->addChild('name', $_POST['name']);
        $new->addChild('number', $_POST['number']);
        $new->addChild('age', $_POST['age']);
        $new->addChild('gender', $_POST['gender']);
        $new->addChild('address', $_POST['address']);
        $new->addChild('membership', $_POST['membership']);
        saveXML($xml, $xmlFile);
        header('Location: table.html?status=added');
        break;

    case 'edit':
        $id = (int) $_POST['id'];
        $xml = loadXML($xmlFile);
        $m = $xml->member[$id];
        if ($_FILES['photo']['tmp_name']) {
            $filename = 'uploads/' . basename($_FILES['photo']['name']);
            move_uploaded_file($_FILES['photo']['tmp_name'], $filename);
            $m->photo = $filename;
        }
        $m->name = $_POST['name'];
        $m->number = $_POST['number'];
        $m->age = $_POST['age'];
        $m->gender = $_POST['gender'];
        $m->address = $_POST['address'];
        $m->membership = $_POST['membership'];
        saveXML($xml, $xmlFile);
        header('Location: table.html?status=edited');
        break;

    case 'delete':
        $id = (int) $_GET['id'];
        $xml = loadXML($xmlFile);
        unset($xml->member[$id]);
        saveXML($xml, $xmlFile);
        header('Location: table.html?status=deleted');
		exit;
        break;

    default:
        echo 'Invalid action';
}
