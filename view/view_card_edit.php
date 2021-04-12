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

                    <input type="hidden" class="form-control" value="<?= $card->get_card_id()?>" name="card_id">
                    <h3>Title</h3>
                    <input type="text" class="form-control" value="<?= isset($proposed_title) ? $proposed_title : $card->get_title()?>" name="title">
                    <h3>Body</h3>
                    <textarea class="form-control" name="body"><?= $card->get_body()?></textarea>
                    <h3>Due date</h3>
                    <input type="date" class="form-control" value="<?= $card->get_due_date()?>" name="title">

                    <?php if (count($card->get_participants()) != 0): ?>
                        <h3>Current participant(s)</h3>
                        <ul>
                            <?php foreach ($card->get_participants() as $participant): ?>
                                <li>
                                    <form action="card/remove_participant" method="post" id=<?= "remove-participant".$participant->get_user_id() ?>>
                                        <span class="participant"><?= $participant->get_full_name().' ('.$participant->get_mail().') ' ?></span>
                                        <input type="hidden" name="card_id" value=<?= $card->get_card_id() ?>>
                                        <input type="hidden" name="participant_id" value=<?= $participant->get_user_id() ?>>
                                        <button type="submit" class="invisible-btn" form=<?= "remove-participant".$participant->get_user_id() ?>>
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <h3>This card has no participant yet.</h3>
                    <?php endif; ?>

                    <?php if (count($card->get_non_participants()) != 0): ?>
                        <h3>Add a new participant :</h3>
                        <form id="add-participant" action="card/add_participant/" class="input-group" method="post">
                            <input type="hidden" name="card_id" value="<?= $card->get_card_id() ?>" form="add-participant">
                            <select name="collaborator_id" class="form-control" form="add-participant">
                                <?php foreach ($card->get_non_participants() as $collaborator): ?>
                                    <option value=<?= $collaborator->get_user_id()?>><?php echo $collaborator->get_full_name()." (".$collaborator->get_mail().")" ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button class="add-participant input-group-text" type="submit" form="add-participant">    
                                <i class="fa fa-plus"></i>
                            </button>
                        </form>  
                    <?php endif; ?>

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