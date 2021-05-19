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
        <script src="lib/jquery-3.6.0.min.js" type="text/javascript"></script>
        <script src="lib/jquery-validation-1.19.3/jquery.validate.min.js" type="text/javascript"></script>
        <script src="lib/MyLib.js" type="text/javascript"></script>
        <script src="lib/jquery-ui-1.12.1.ui-lightness/jquery-ui.min.js" type="text/javascript"></script>
        <link href="lib/jquery-ui-1.12.1.ui-lightness/jquery-ui.min.css" rel="stylesheet" type="text/css"/>
        <link href="lib/jquery-ui-1.12.1.ui-lightness/jquery-ui.theme.min.css" rel="stylesheet" type="text/css"/>
        <link href="lib/jquery-ui-1.12.1.ui-lightness/jquery-ui.structure.min.css" rel="stylesheet" type="text/css"/>
        <script>
            $(function() {
                var cardsUpdate;
                $( ".cards" ).sortable({
                    connectWith: ".cards",
                    update: function(event, ui) {  
                        cardsUpdate = {
                            column_info: {
                                id: $(this).attr('column-id'),
                                moved_card_id: $(ui.item).attr('card-id'),
                                cards_id: $(this).sortable('toArray', { attribute: 'card-id' })
                            }
                        };
                    },
                    stop:function(){
                        $.post("card/change_cards_in_column_service/",
                            cardsUpdate
                        ).fail(function(){
                            alert("<tr><td>Error encountered!</td></tr>");
                        });
                    }
                }).disableSelection();

                
                var columnsUpdate;
                $( ".columns" ).sortable({
                    connectWith: ".columns",
                    update: function(event, ui) {  
                        columnsUpdate = {
                            board_info: {
                                id: <?= $board->get_board_id() ?>,
                                moved_column_id: $(ui.item).attr('column-id'),
                                columns_id: $(this).sortable('toArray', { attribute: 'column-id' })
                            }
                        };
                    },
                    stop:function(){
                        $.post("column/change_columns_in_board_service/",
                            columnsUpdate
                        ).fail(function(){
                            alert("<tr><td>Error encountered!</td></tr>");
                        });
                    }
                }).disableSelection();

                $('#add-card').validate({
                    rules: {
                        title: {
                            minlength: 3,
                            remote: {
                                url: 'card/available_card_title_service',
                                type: 'post',
                                data:  {
                                    card_title: function() { 
                                        return $("#input-card-name").val();
                                    },
                                    board_id: <?= $board->get_board_id() ?>
                                }
                            }
                        }
                    },
                    messages: {
                        title: {
                            minlength: 'minimum 3 characters',
                            remote: 'this card title already exists'
                        }
                    }
                });

                $('#add-column').validate({
                    rules: {
                        title: {
                            minlength: 3,
                            remote: {
                                url: 'column/available_column_title_service',
                                type: 'post',
                                data:  {
                                    column_title: function() { 
                                        return $("#input-column-name").val();
                                    },
                                    board_id: <?= $board->get_board_id() ?>
                                }
                            }
                        }
                    },
                    messages: {
                        title: {
                            minlength: 'minimum 3 characters',
                            remote: 'this column title already exists'
                        }
                    }
                });

                document.getElementById('delete_board').setAttribute("href", "javascript:deleteBoardConfirm(\"" + <?= $board->get_board_id()?>  + "\")");
                <?php foreach($board->get_columns() as $column): ?>
                    document.getElementById("<?= 'delete_column'.$column->get_column_id() ?>").setAttribute("href", "javascript:deleteColumnConfirm(\"" + <?= $column->get_column_id()?>  + "\")");
                    <?php foreach($column->get_cards() as $card): ?>
                        document.getElementById("<?= 'delete_card'.$card->get_card_id() ?>").setAttribute("href", "javascript:deleteCardConfirm(\"" + <?= $card->get_card_id()?>  + "\")");
                    <?php endforeach; ?>
                <?php endforeach; ?>
            });

            function deleteBoardConfirm(id) {
                $('#delete_board_confirm_dialog').dialog({
                    resizable: false,
                    height: 300,
                    width: 600,
                    modal: true,
                    autoOpen: true,
                    buttons: {
                        Confirm: function () {
                            deleteBoard(id);
                            $(this).dialog("close");
                        },
                        Cancel: function () {
                            $(this).dialog("close");
                        }
                    }
                });
            }

            function deleteBoard(id){
                $.post("board/delete_service/",
                    {"board_id": id},
                    function (data) {
                        window.location.replace("board/index");
                    }
                ).fail(function(){
                    alert("<p>Error encountered while retrieving the messages!</p>");
                });
            }

            function deleteColumnConfirm(id) {
                $('#delete_column_confirm_dialog').dialog({
                    resizable: false,
                    height: 300,
                    width: 600,
                    modal: true,
                    autoOpen: true,
                    buttons: {
                        Confirm: function () {
                            deleteColumn(id);
                            $(this).dialog("close");
                        },
                        Cancel: function () {
                            $(this).dialog("close");
                        }
                    }
                });
            }

            function deleteColumn(id){
                $.post("column/delete_service/",
                    {"column_id": id},
                    function (data) {
                        window.location.replace("board/board/" + <?= $board->get_board_id()?>);
                    }
                ).fail(function(){
                    alert("<p>Error encountered while retrieving the messages!</p>");
                });
            }

            function deleteCardConfirm(id) {
                $('#delete_card_confirm_dialog').dialog({
                    resizable: false,
                    height: 300,
                    width: 600,
                    modal: true,
                    autoOpen: true,
                    buttons: {
                        Confirm: function () {
                            deleteCard(id);
                            $(this).dialog("close");
                        },
                        Cancel: function () {
                            $(this).dialog("close");
                        }
                    }
                });
            }

            function deleteCard(id){
                $.post("card/delete_service/",
                    {"card_id": id},
                    function (data) {
                        window.location.replace("board/board/" + <?= $board->get_board_id()?>);
                    }
                ).fail(function(){
                    alert("<p>Error encountered while retrieving the messages!</p>");
                });
            }

        </script>
    </head>
    <body>
        <?php
            $menu_title = $board->get_menu_title();
            $menu_subtitle = "Boards";
            include("menu.php");
        ?>
        <div class="content">
            <div class="header">
                <h2>
                    <?= $board->get_menu_title() ?> 
                    <a href=<?= "board/edit/".$board->get_board_id() ?> class="invisible-link"><i class="fa fa-edit"></i></a>
                    <?php if($board && ($user->is_admin() || $board->get_author_id() === $user->get_user_id())): ?>
                        <a href=<?= "board/collaborators/".$board->get_board_id() ?> class="invisible-link"><i class="fa fa-users"></i></a>
                    <?php endif; ?>
                    <a id="delete_board" href=<?= "board/delete/".$board->get_board_id() ?> class="invisible-link"><i class="fa fa-trash"></i></a>
                </h2>
                Created <?= $board->get_duration_since_creation() ?> ago by <a href="board/index"><?= $board->get_author_name() ?></a>. <?= $board->get_last_modification()?"Modified ".$board->get_duration_since_last_edit()." ago.":"Never modified." ?>
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
            <div class="board">
                <div class="columns">
                    <?php foreach($board->get_columns() as $column): ?>
                        <div column-id="<?=$column->get_column_id()?>" class="column">
                            <div class="column-title">
                                <span><?= $column->get_title()?> </span>
                                <a href=<?= "column/edit/".$column->get_column_id() ?> class="invisible-link"><i class="fa fa-edit"></i></a> 
                                <a id=<?= 'delete_column'.$column->get_column_id() ?> href=<?= "column/delete/".$column->get_column_id() ?> class="invisible-link"><i class="fa fa-trash"></i></a>
                                <noscript>
                                    <?php if($column->get_position() != $column->get_first_position()): ?>
                                        <form  action="column/move" method="post" id=<?= "move-left".$column->get_column_id() ?>>
                                            <input type="hidden" name="direction" value="left">
                                            <input type="hidden" name="board_id" value=<?= $board->get_board_id() ?>>
                                            <input type="hidden" name="column_id" value=<?= $column->get_column_id() ?>>
                                            <button type="submit" class="invisible-btn" form=<?= "move-left".$column->get_column_id() ?>>
                                                <i class="fa fa-arrow-circle-left"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>                            
                                    <?php if($column->get_position() != $column->get_last_position()): ?>
                                        <form  action="column/move" method="post" id=<?= "move-right".$column->get_column_id() ?>>
                                            <input type="hidden" name="direction" value="right">
                                            <input type="hidden" name="board_id" value=<?= $board->get_board_id() ?>>
                                            <input type="hidden" name="column_id" value=<?= $column->get_column_id() ?>>
                                            <button type="submit" class="invisible-btn" form=<?= "move-right".$column->get_column_id() ?>>
                                                <i class="fa fa-arrow-circle-right"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </noscript>
                            </div>  
                            <div column-id="<?=$column->get_column_id()?>" class="cards">                  
                                <?php foreach($column->get_cards() as $card): ?>
                                        <div card-id="<?=$card->get_card_id()?>" class="<?="card".($card->past_due_date() ? " expired" : "")?>">
                                            <div class="card-title">
                                            <a href=<?= "card/index/".$card->get_card_id() ?>><?=$card->get_title()?></a>
                                            </div>
                                            <div class="card-buttons">
                                                <form  action="card/move/" method="post">
                                                    <a href=<?= "card/view/".$card->get_card_id() ?> class="invisible-link"><i class="fa fa-eye"></i></a> 
                                                    <a href=<?= "card/edit/".$card->get_card_id() ?> class="invisible-link"><i class="fa fa-edit"></i></a> 
                                                    <a id=<?= 'delete_card'.$card->get_card_id() ?> href=<?= "card/delete_confirm/".$card->get_card_id() ?> class="invisible-link"><i class="fa fa-trash"></i></a> 
                                                    <noscript>
                                                    <input type="hidden" name="card_id" value=<?= $card->get_card_id() ?>>
                                                    <?php if($card->get_position() != $card->get_first_position()): ?>
                                                        <button type="submit" class="invisible-btn-card" name="direction" value="up">
                                                            <i class="fa fa-arrow-circle-up"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                    <?php if($card->get_position() != $card->get_last_position()): ?>
                                                        <button type="submit" class="invisible-btn-card" name="direction" value="down">
                                                            <i class="fa fa-arrow-circle-down"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                    <?php if($column->get_position() != $column->get_first_position()): ?>
                                                        <button type="submit" class="invisible-btn-card" name="direction" value="left">
                                                            <i class="fa fa-arrow-circle-left"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                    <?php if($column->get_position() != $column->get_last_position()): ?>
                                                        <button type="submit" class="invisible-btn-card" name="direction" value="right" >
                                                            <i class="fa fa-arrow-circle-right"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                    </noscript>
                                                </form>
                                            </div>
                                        </div>
                                
                                    <?php endforeach; ?>   
                                </div>  
                            <form action=<?= "card/add/" ?> class="input-group add-card" method="post" id="add-card">
                                <?php if(isset($new_card) && $new_card->get_column_id() === $column->get_column_id()): ?>
                                    <input id="input-card-name" name="title" type="text" placeholder="Add a card" class="form-control card-name" value="<?= $new_card->get_title()?>">
                                <?php else: ?>
                                    <input id="input-card-name" name="title" type="text" placeholder="Add a card" class="form-control">
                                <?php endif; ?>
                                <button class="input-group-text" type="submit" name="column_id" value="<?=$column->get_column_id()?>">    
                                    <i class="fa fa-plus"></i>
                                </button>
                            </form>  
                        </div>
                    <?php endforeach; ?> 
                </div>
                <div class="column">
                    <form action=<?= "column/index/".$board->get_board_id() ?> id="add-column" class="input-group add-column" method="post">
                        <?php if(isset($add_column_title)): ?>
                            <input id="input-column-name" name="title" type="text" placeholder="Add a column" class="form-control" value="<?= $add_column_title?>"> 
                        <?php else: ?>
                            <input id="input-column-name" name="title" type="text" placeholder="Add a column" class="form-control"> 
                        <?php endif; ?>
                        <button class="input-group-text" type="submit" form="add-column">    
                            <i class="fa fa-plus"></i>
                        </button>
                    </form> 
                </div>
            </div>
        </div>
        <div id="delete_board_confirm_dialog" title="Confirm Board Deletion" hidden>
            <p>Please confirm that you want to delete this board.</p>
            <p>This operation can't be reversed!</p>
        </div>
        <div id="delete_column_confirm_dialog" title="Confirm Column Deletion" hidden>
            <p>Please confirm that you want to delete this column.</p>
            <p>This operation can't be reversed!</p>
        </div>
        <div id="delete_card_confirm_dialog" title="Confirm Card Deletion" hidden>
            <p>Please confirm that you want to delete this card.</p>
            <p>This operation can't be reversed!</p>
        </div>
    </body>
</html>