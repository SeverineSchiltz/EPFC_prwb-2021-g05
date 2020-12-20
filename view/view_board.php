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
        <link href="css/board.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <?php
            ob_start();
            include("menu.php");
            $buffer=ob_get_contents();
            ob_end_clean();

            $buffer=str_replace("%TITLE%",$board->get_title(),$buffer);
            $buffer=str_replace("%SUBTITLE%","Boards",$buffer);
            echo $buffer;
        ?>
        <div class="content">
            <div class="header">
                <h2>
                    <?php echo $board->get_title() ?> 
                    <i class="fa fa-edit"></i> 
                    <i class="fa fa-trash"></i>
                </h2>
                Created 1 month ago by <a href="board/index">Boris Verhaegen</a>. Never modified.
            </div>
            <div class="columns">
                <?php foreach($columns as $column): ?>
                    <div class="column">
                        <div class="column-title">
                            <?= $column->title?> 
                            <i class="fa fa-edit"></i> 
                            <i class="fa fa-trash"></i> 
                            <i class="fa fa-arrow-circle-left"></i> 
                            <i class="fa fa-arrow-circle-right"></i>
                        </div>                        
                        <?php foreach($column->get_cards() as $card): ?>
                            <div class="card">
                                <div class="card-title">
                                <a href=<?php echo "card/view/".$card->card_id ?>><?=$card->title?></a>
                                </div>
                                <div class="card-buttons">
                                    <i class="fa fa-eye"></i> 
                                    <i class="fa fa-edit"></i> 
                                    <i class="fa fa-trash"></i> 
                                    <i class="fa fa-arrow-circle-up"></i> 
                                    <i class="fa fa-arrow-circle-down"></i>
                                    <i class="fa fa-arrow-circle-left"></i> 
                                    <i class="fa fa-arrow-circle-right"></i>
                                </div>
                            </div>
                        <?php endforeach; ?>     
                        <form action="card/add" id="add-card" class="input-group add-card">
                            <input id="add-card" name="add-card" type="add-card" value="<?= $title ?>" placeholder="Add a card" class="form-control">
                            <button class="input-group-text" type="submit" form="add-card">    
                                <i class="fa fa-plus"></i>
                            </button>
                        </form>  
                    </div>
                <?php endforeach; ?> 
                <div class="column">
                    <form action="column/add" id="add-column" class="input-group add-column">
                        <input id="add-column" name="add-column" type="add-column" value="<?= $title ?>" placeholder="Add a column" class="form-control">
                        <button class="input-group-text" type="submit" form="add-column">    
                            <i class="fa fa-plus"></i>
                        </button>
                    </form>  
                </div>
            </div>
        </div>
    </body>
</html>