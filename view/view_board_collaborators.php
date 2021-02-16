<?php
    $menu_title = $board->get_menu_title();
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
        <link href="css/board_collaborators.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <?php include("menu.php");?>
        <div class="content">
            <h2><?php echo $board->get_menu_title()?> : Collaborators</h2>
            <div class="collaborators">
                <h3>Current collaborator(s) :</h3>
                <ul>
                    <?php foreach ($board->get_collaborators() as $collaborator): ?>
                        <li>
                            <div class="one-collaborator">
                                <form  id="collaborator" action="board/collaborator_delete/" method="post">
                                    <?php echo $collaborator->get_full_name()." (".$collaborator->get_mail().")" ?>
                                    <input type="hidden" name="board_id" value=<?= $board->get_board_id() ?>>
                                    <input type="hidden" name="collaborator_id" value=<?= $collaborator->get_user_id() ?>>
                                    <button type="submit" class="invisible-btn">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <?php if (count($board->get_non_collaborators()) != 0): ?>
                    <h3>Add a new collaborator :</h3>
                    <form id="add-collaborators" action="board/add_collaborator/" class="input-group" method="post">
                        <input type="hidden" name="board_id" value="<?= $board->get_board_id() ?>" form="add-collaborators">
                        <select name="new_collaborator_id" class="form-control" form="add-collaborators">
                            <?php foreach ($board->get_non_collaborators() as $new_collaborator): ?>
                                <option value=<?= $new_collaborator->get_user_id()?>><?php echo $new_collaborator->get_full_name()." (".$new_collaborator->get_mail().")" ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button class="add-collaborators input-group-text" type="submit" form="add-collaborators">    
                            <i class="fa fa-plus"></i>
                        </button>
                    </form>  
                <?php endif; ?>
            </div>
        </div>
    </body>
</html>