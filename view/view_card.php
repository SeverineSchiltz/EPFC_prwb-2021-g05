<?php
    $menu_title = 'Card "'.$card->title.'"';
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
        <link href="css/card.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <?php include("menu.php");?>
        <div class="content">
            <div class="card-header">
                <h2>
                    <?= $menu_title?> 
                    <a href=<?= "card/edit/".$card->card_id ?> class="invisible-link"><i class="fa fa-edit"></i></a>
                    <a href=<?= "card/delete_confirm/".$card->card_id ?> class="invisible-link"><i class="fa fa-trash"></i></a>
                </h2>
                <h4>Created by <span><?= $card->get_author_name() ?></span></h4>
            </div>
            <div class="card-body">
                <h3>Body</h3>
            </div>
        </div>
    </body>
</html>