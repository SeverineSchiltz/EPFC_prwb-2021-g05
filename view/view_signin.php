<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Log In</title>
        <base href="<?= $web_root ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link href="css/style.css" rel="stylesheet" type="text/css"/>
        <link href="css/menu.css" rel="stylesheet" type="text/css"/>
        <link href="css/signin.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <?php include 'menu.php'; ?>
        <div class="content container">
            <div class="row">
                <div class="col-sm-3"></div>
                <div class="col-sm-6 form">
                    <h2>Sign in</h2>
                    <hr>
                    <form action="user/login" method="post">
                        <div class="form-group">
                            <div class="input-group mail">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fa fa-user"></i>
                                    </span>
                                </div> 
                                <input id="mail" name="mail" type="text" value="<?= $mail ?>" placeholder="Mail" class="form-control">
                            </div>
                            
                            <div class="input-group password">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fa fa-lock"></i>
                                    </span>
                                </div>              
                                <input id="password" name="password" type="password" value="<?= $password ?>" placeholder="*****" class="form-control">
                            </div>

                            <input type="submit" value="Login" class="btn btn-primary submit">
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-sm-3"></div>
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
        </div>
    </body>
</html>
