<!DOCTYPE html>
<html lang="ru">
<head>
    <!-- HEAD -->
    <?php 
        require_once 'mysql_connect.php';

        $sql = 'SELECT * FROM `articles` WHERE `id` = :id';
        $query = $pdo->prepare($sql);
        $query->execute(['id'=>$_GET['id']]);

         $article = $query->fetch(PDO::FETCH_OBJ);

        $website_title = $article->title;
        include 'blocks/head.php'; 
    ?>
</head>
<body>
    <!-- HEADER -->
    <?php include 'blocks/header.php'; ?>

    <!-- Main Body -->
    <main class="container mt-5"> 
        <div class="row">
            <div class="col-md-8 mb-3">
                <div class="jumbotron">
                    <h1><?=$article->title?></h1>
                    <p><b>Автор статьи: </b><mark><?=$article->avtor?></mark><br></p>
                    <?php
                        $date = date('d ', $article->date);
                        $array = ["Января", "Февраля", "Марта", "Апреля", "Мая", "Июня", "Июля", "Августа", 
                        "Сентября", "Октября", "Ноября", "Декабря"];
                        $date .= $array[date('n', $article->date) - 1];
                        $date .= date(' H:i', $article->date+(3600 * 3));
                    ?>
                    <p><b>Время публикации: </b><u><?=$date?></u><br></p>
                 
                    <p>
                        <?=$article->intro?>
                        <br><br>
                        <?=$article->text?>
                    </p>
                </div>
                <?php
                    $sql = 'SELECT * FROM `commen` WHERE `article_id` = :id ORDER BY `id` DESC';
                    $query = $pdo->prepare($sql);
                    $query->execute(['id' => $_GET['id']]);
                    $comments = $query->fetchAll(PDO::FETCH_OBJ);

                    foreach($comments as $comment){
                        echo "<div class='alert alert-info mb-2'>
                            <h4>$comment->name</h4>
                            <p>$comment->mess</p>
                            <img src='uploads/$comment->image'>
                            
                        </div>";
                    }
                ?>
                <h3 class="mt-5">Отзывы</h3>
                <form action="news.php?id=<?=$_GET['id']?>" method="post" enctype="multipart/form-data">
                    <label for="username">Ваше имя</label>
                    <input type="text" name="username" value="<?=$_COOKIE['login']?>" id="username" class="form-control">

                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control">

                    <label for="mess">Сообщение</label>
                    <textarea name="mess" id="mess" class="form-control"></textarea>

                    <input type="file" name="image" id="image"> 

                    <button type="submit" id="mess_send" class="btn btn-success mt-5 mb-5">
                        Добавить комментарий
                    </button>
                </form>
                
                <!--  -->
                <?php 
                    if($_POST['username'] != '' && $_POST['mess'] != ''){
                        $username = trim(filter_var($_POST['username'], FILTER_SANITIZE_STRING));
                        $email = trim(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL));
                        $mess = trim(filter_var($_POST['mess'], FILTER_SANITIZE_STRING));
                        
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
                    }
                ?>
            </div>
            <!-- ASIDE -->
            <?php include 'blocks/aside.php'; ?>
        </div>
    </main>

    <!-- FOOTER -->
    <?php include 'blocks/footer.php'; ?>

    <!-- <script src="css/jquery.min.js"></script>

    <script>
        $('#errorBlock').hide();
        $('#reg_user').click(function(){
            var name = $('#username').val();
            var email = $('#email').val();
            var mess = $('#mess').val();
            var image = $('#image').val();
            

            $.ajax({
                url:'ajax/reg.php',
                type:'POST',
                cache: false,
                data: {'username' : name, 'email' : email, 'mess' : mess, 'image' : image},
                dataType: 'html',
                success: function(data){
                    if(data == 'Готово'){
                        $('#reg_user').text('Все готово');
                        $('#errorBlock').hide();
                    }else{
                        $('#errorBlock').show();
                        $('#errorBlock').text(data); 
                    }
                }
            });
        })
    </script> -->
</body>
</html>