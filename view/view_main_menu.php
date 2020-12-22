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

            <div class="boards">
                <!-- your boards -->
                <?php foreach($personal_boards as $board): ?>
                    <form class='form-my-boards' action=<?php echo "board/board/".$board->board_id ?> method='post'>
                        <button type="submit" class="btn btboardsMe">
                            <p class="board-title"><?= $board->title?> (<?= $board->get_nb_columns()?> columns)</p>
                        </button>
                    </form>
                <?php endforeach; ?> 
                    
                <!-- add board form -->
                <form id="form-add-board" action="board" method="post" class="input-group form-my-boards">
                    <input id="input-board-name" name="board-name" type="text" placeholder="Add a board" class="form-control">
                    <button class="input-group-text btt-add-board" type="submit"> 
                        <i class="fa fa-plus"></i>
                    </button>
                </form>
            </div>
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
            <div class="boards">               
            <!-- others' boards -->
                <?php foreach($other_boards as $board): ?>
                <form class='link' action=<?php echo "board/board/".$board->board_id ?> method='post'>
                    <button type="submit" class="btn btboardsOther">
                        <p class="board-title"><?= $board->title?> (<?= $board->get_nb_columns()?> columns)</p>
                        <p class="author-name">by <?=$board->author->full_name?></p>
                    </button>
                </form>
                <?php endforeach; ?> 
            </div>
        </div>
    </body>
</html>