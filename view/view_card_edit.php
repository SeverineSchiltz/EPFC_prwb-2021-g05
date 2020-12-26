<?php
    $menu_title = 'Card "'.$card->get_title().'"';
    $menu_subtitle = "Boards";
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title><?= $menu_title?></title>
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
                <h4>Created by <?= $card->get_author_name() ?> <?= $card->get_duration_since_creation() ?> <?= $card->get_duration_since_last_edit() ?></h4>
            </div>
            <div class="card-body">
                <form>
                    <h3>Title</h3>
                    <input class="form-control" value="<?= $card->get_title()?>"></input>
                    <h3>Body</h3>
                    <textarea class="form-control"><?= $card->get_body()?></textarea>
                    <h3>Board</h3>
                    <input readonly class="form-control" value="<?= $card->get_board_title()?>"></input>
                    <h3>Column</h3>
                    <input readonly class="form-control" value="<?= $card->get_column_title()?>"></input>
                    <div class="buttons">
                        <a href=<?= "card/view/".$card->get_card_id()?> class="btn btn-primary cancel">Cancel</a>
                        <input type="submit" value="Edit this card" class="btn btn-primary submit">   
                    </div>   
                </form>
            </div>
        </div>
    </body>
</html>