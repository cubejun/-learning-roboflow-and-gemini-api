<?php
    // 데이터베이스 연결 설정
    $servername = "localhost";
    $username = "root";
    $password = "plab";
    $dbname = "exampledb";
    $conn = new mysqli($servername, $username, $password, $dbname);
    // 연결 확인
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    // 데이터베이스에서 데이터 가져오기
    $sql = "SELECT id, image_data1, image_data2, result, diagnosis1, diagnosis2, audio_data FROM dental";
    $result = $conn->query($sql);
    $images = [];
    if ($result->num_rows > 0) {
        // 결과 출력
        while($row = $result->fetch_assoc()) {
            $image = [
                "id" => $row["id"],
                "image_data1" => $row["image_data1"],
                "image_data2" => $row["image_data2"],
                "result" => $row["result"],
                "diagnosis1" => $row["diagnosis1"],
                "diagnosis2" => $row["diagnosis2"],
                "audio_data" => $row["audio_data"]
            ];
            array_push($images, $image);
        }
    }
    $response = [
        "success" => true,
        "images" => $images
    ];
    header('Content-Type: application/json');
    echo json_encode($response);
    $conn->close();
?>
