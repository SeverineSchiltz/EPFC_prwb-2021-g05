<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Delete</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link href="css/style.css" rel="stylesheet" type="text/css"/>
        <link href="css/menu.css" rel="stylesheet" type="text/css"/>
        <link href="css/delete.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <?php
                $menu_title = "";
                $menu_subtitle = "Boards";
                include("menu.php");
            ?>
        <div class="content container">
            <div class="row">
                <div class="col-sm-3"></div>
                <div class="col-sm-6 form">
                    <i class="fa fa-trash big-icon"></i>
                    <h1>Are you sure?</h1>
                    <hr>
                    <span>Do you really want to delete this card?</span>
                    <span>This process cannot be undone.</span>
                    <div class="buttons">
                        <a href=<?= "board/board/".$column->get_board_id() ?> class="btn btn-secondary">Cancel</a>
                        <form action=<?= "column/delete/".$column->get_column_id() ?> method="post">
                            <input type="hidden" name="confirmation" value="true">
                            <input type="hidden" name="column_id" value=<?= $column->get_column_id() ?>>
                            <input type="submit" value="Delete" class="btn btn-danger submit">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
