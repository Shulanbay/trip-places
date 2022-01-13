<?php
    $username = trim(filter_var($_POST['username'], FILTER_SANITIZE_STRING));
    $email = trim(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL));
    $mess = trim(filter_var($_POST['mess'], FILTER_SANITIZE_STRING));
    

    $error = '';
    if(strlen($username) <= 3){
        $error = 'Введите имя';
    }else if(strlen($email) <= 3){
        $error = 'Введите email';
    }else if(strlen($mess) <= 3){
        $error = 'Введите отзыв';
    }

    if($error != ''){
        echo $error;
        exit();
    }

    require_once '../mysql_connect.php';
        
        if (isset($_FILES['image'])) {
        
            echo "<pre>";
            print_r($_FILES['image']);
            echo "</pre>";

            $img_name = $_FILES['image']['name'];
            $img_size = $_FILES['image']['size'];
            $tmp_name = $_FILES['image']['tmp_name'];
            $error = $_FILES['image']['error'];

            if ($error === 0) {
                if ($img_size > 100000) {
                    $em = "Sorry, your file is too large.";
                }else {
                    $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
                    $img_ex_lc = strtolower($img_ex);

                    $allowed_exs = array("jpg", "jpeg", "png"); 

                    if (in_array($img_ex_lc, $allowed_exs)) {
                        $new_img_name = uniqid("IMG-", true).'.'.$img_ex_lc;
                        $img_upload_path = 'uploads/'.$new_img_name;
                        move_uploaded_file($tmp_name, $img_upload_path);

                        $sql = 'INSERT INTO commen(name, email, mess, image, article_id) VALUES(?, ?, ?, ?, ?)'; 
                        $query = $pdo->prepare($sql);
                        $query->execute([$username, $email, $mess, $new_img_name, $_GET['id']]);
                        // // Insert into Database
                        // $sql = "INSERT INTO images(image_url) 
                        //         VALUES('$new_img_name')";
                        // mysqli_query($conn, $sql);
                    }else {
                        $em = "You can't upload files of this type";
                    }
                }
            }else {
                $em = "unknown error occurred!";
            }

        }

    echo 'Готово';
?>

