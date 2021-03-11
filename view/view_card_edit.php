<?php
    $menu_title = 'Card "'.$card->get_title().'"';
    $menu_subtitle = "Boards";
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Edit a card</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link href="css/style.css" rel="stylesheet" type="text/css"/>
        <link href="css/menu.css" rel="stylesheet" type="text/css"/>
        <link href="css/card_edit.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <?php include("menu.php");?>
        <div class="content">
            <div class="card-header">
                <h2>Edit a card</h2>
                <h4>Created by <span><?= $card->get_author_name() ?></span> <?= $card->get_duration_since_creation() ?> ago. <?= $card->get_last_modification()?"Modified ".$card->get_duration_since_last_edit()." ago.":"Never modified." ?></h4>
            </div>
            <div class="card-body">
                <form action=<?= "card/save/"?> method="post">
                    <input type="hidden" class="form-control" value="<?= $card->get_card_id()?>" name="card_id">
                    <h3>Title</h3>
                    <input type="text" class="form-control" value="<?= isset($proposed_title) ? $proposed_title : $card->get_title()?>" name="title">
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
                    <h3>Body</h3>
                    <textarea class="form-control" name="body"><?= $card->get_body()?></textarea>
                    <h3>Board</h3>
                    <input readonly type="text" class="form-control" value="<?= $card->get_board_title()?>">
                    <h3>Column</h3>
                    <input readonly type="text" class="form-control" value="<?= $card->get_column_title()?>">
                    <div class="buttons">
                        <a href=<?= "card/view/".$card->get_card_id()?> class="btn btn-primary cancel">Cancel</a>
                        <input type="submit" value="Edit this card" class="btn btn-primary submit">   
                    </div>  
                </form>
            </div>
        </div>
    </body>
</html>