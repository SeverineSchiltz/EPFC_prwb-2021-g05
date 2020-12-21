<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Boards</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link href="css/style.css" rel="stylesheet" type="text/css"/>
        <link href="css/menu.css" rel="stylesheet" type="text/css"/>
        <link href="css/main.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <?php
            ob_start();
            include("menu.php");
            $buffer=ob_get_contents();
            ob_end_clean();

            $buffer=str_replace("%TITLE%","Boards",$buffer);
            $buffer=str_replace("%SUBTITLE%","",$buffer);
            echo $buffer;
        ?>
        <div class="content">
            <h2>Your boards</h2>

            <!-- your boards -->

            <?php foreach($personal_boards as $board): ?>
                <a href=<?php echo "board/board/".$board->board_id ?>><?= $board->title?> (<?= $board->get_nb_columns()?> columns)</a>
             <?php endforeach; ?> 
                
            <!-- add board form -->
            <!--    
            <form id="board_form" action="boards.php?param1=<?= $recipient->mail ?>" method="post">
                <input id="private" name="private" type="text" placeholder="Add a board"><input id="post" type="submit" value="Post">
            </form>
            -->
            <form id="board_form" action="board" method="post">
                <input id="private" name="private" type="text" placeholder="Add a board"><input id="post" type="submit" value="Post">
            </form>
            
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

            <h2>Others' boards</h2>

            <!-- others' boards -->
            <?php foreach($other_boards as $board): ?>
                <a href=<?php echo "board/board/".$board->board_id ?>><?= $board->title?> (<?= $board->get_nb_columns()?> columns)</a>
            <?php endforeach; ?> 
        </div>
    </body>
</html>