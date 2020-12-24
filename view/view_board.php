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
            $title = $board->get_menu_title();
            $subtitle = "Boards";
            include("menu.php");
        ?>
        <div class="content">
            <div class="header">
                <h2>
                    <?= $board->get_menu_title() ?> 
                    <i class="fa fa-edit"></i> 
                    <a href=<?= "board/delete/".$board->board_id ?> class="invisible-link"><i class="fa fa-trash"></i></a>
                </h2>
                Created <?= $board->get_duration_since_creation() ?> ago by <a href="board/index"><?= $board->author->full_name ?></a>. <?= $board->get_last_modification()?"Modified ".$board->get_duration_since_last_edit()." ago.":"Never modified." ?>
            </div>
            <div class="columns">
                <?php foreach($board->get_columns() as $column): ?>
                    <div class="column">
                        <div class="column-title">
                            <?= $column->title?> 
                            <a href=<?= "column/edit/".$column->column_id ?> class="invisible-link"><i class="fa fa-edit"></i></a> 
                            <a href=<?= "column/delete/".$column->column_id ?> class="invisible-link"><i class="fa fa-trash"></i></a>
                            <?php if($column->position != $column->get_first_position()): ?>
                                <form  action="column/move" method="post" id=<?= "move-left".$column->column_id ?>>
                                    <input type="hidden" name="direction" value="left">
                                    <input type="hidden" name="board_id" value=<?= $board->board_id ?>>
                                    <input type="hidden" name="column_id" value=<?= $column->column_id ?>>
                                    <button type="submit" class="invisible-btn" form=<?= "move-left".$column->column_id ?>>
                                        <i class="fa fa-arrow-circle-left"></i>
                                    </button>
                                </form>
                            <?php endif; ?>                            
                            <?php if($column->position != $column->get_last_position()): ?>
                                <form  action="column/move" method="post" id=<?= "move-right".$column->column_id ?>>
                                    <input type="hidden" name="direction" value="right">
                                    <input type="hidden" name="board_id" value=<?= $board->board_id ?>>
                                    <input type="hidden" name="column_id" value=<?= $column->column_id ?>>
                                    <button type="submit" class="invisible-btn" form=<?= "move-right".$column->column_id ?>>
                                        <i class="fa fa-arrow-circle-right"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>                        
                        <?php foreach($column->get_cards() as $card): ?>
                            <div class="card">
                                <div class="card-title">
                                <a href=<?= "card/index/".$card->card_id ?>><?=$card->title?></a>
                                </div>
                                <div class="card-buttons">
                                    <form  action="card/move/" method="post">
                                        <a href=<?= "card/view/".$card->card_id ?> class="invisible-link"><i class="fa fa-eye"></i></a> 
                                        <a href=<?= "card/edit/".$card->card_id ?> class="invisible-link"><i class="fa fa-edit"></i></a> 
                                        <a href=<?= "card/delete_confirm/".$card->card_id ?> class="invisible-link"><i class="fa fa-trash"></i></a> 
                                        <input type="hidden" name="card_id" value=<?= $card->card_id ?>>
                                        <?php if($card->position != $card->get_first_position()): ?>
                                            <button type="submit" class="invisible-btn-card" name="direction" value="up">
                                                <i class="fa fa-arrow-circle-up"></i>
                                            </button>
                                        <?php endif; ?>
                                        <?php if($card->position != $card->get_last_position()): ?>
                                            <button type="submit" class="invisible-btn-card" name="direction" value="down">
                                                <i class="fa fa-arrow-circle-down"></i>
                                            </button>
                                        <?php endif; ?>
                                        <?php if($column->position != $column->get_first_position()): ?>
                                            <button type="submit" class="invisible-btn-card" name="direction" value="left">
                                                <i class="fa fa-arrow-circle-left"></i>
                                            </button>
                                        <?php endif; ?>
                                        <?php if($column->position != $column->get_last_position()): ?>
                                            <button type="submit" class="invisible-btn-card" name="direction" value="right" >
                                                <i class="fa fa-arrow-circle-right"></i>
                                            </button>
                                        <?php endif; ?>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>     
                        <form action=<?= "card/add/".$column->column_id ?> id=<?= "add-card".$column->column_id ?> class="input-group add-card">
                            <input name="title" type="text" placeholder="Add a card" class="form-control">
                            <button class="input-group-text" type="submit" form=<?= "add-card".$column->column_id ?>>    
                                <i class="fa fa-plus"></i>
                            </button>
                        </form>  
                    </div>
                <?php endforeach; ?> 
                <div class="column">
                    <form action=<?= "column/index/".$board->board_id ?> id="add-column" class="input-group add-column" method="post">
                        <input name="title" type="text" placeholder="Add a column" class="form-control">
                        <button class="input-group-text" type="submit" form="add-column">    
                            <i class="fa fa-plus"></i>
                        </button>
                    </form>  
                </div>
            </div>
            <?php if (count($errors) != 0): ?>
                <div class='errors'>
                    <p>The following error(s) occured :</p>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </body>
</html>