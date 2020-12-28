<?php
    $menu_title = $board->get_menu_title();
    $menu_subtitle = "Boards";
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit a board</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link href="css/style.css" rel="stylesheet" type="text/css"/>
        <link href="css/menu.css" rel="stylesheet" type="text/css"/>
        <link href="css/board_edit.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <?php include("menu.php");?>
        <div class="content">
            <div class="card-header">
                <h2>Edit a board</h2>
                <h4>Created by <?= $board->get_author_name() ?> <?= $board->get_duration_since_creation() ?> <?= $board->get_duration_since_last_edit() ?></h4>
            </div>
            <div class="card-body">
                <form action=<?= "board/save/"?> method="post">
                    <input type="hidden" class="form-control" value="<?= $board->get_board_id()?>" name="board_id">
                    <h3>Title</h3>
                    <input type="text" class="form-control" value="<?= $board->get_title()?>" name="title">
                    <?php if (count($errors) != 0): ?>
                        <div class='errors'>
                            <p>Please correct the following error(s) :</p>
                            <ul>
                                <?php foreach ($errors as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <div class="buttons">
                        <a href=<?= "board/board/".$board->get_board_id()?> class="btn btn-primary cancel">Cancel</a>
                        <input type="submit" value="Edit this board" class="btn btn-primary submit">   
                    </div>  
                </form>
            </div>
        </div>
    </body>
</html>