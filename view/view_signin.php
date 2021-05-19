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
        <script src="lib/jquery-3.6.0.min.js" type="text/javascript"></script>
        <script src="lib/jquery-validation-1.19.3/jquery.validate.min.js" type="text/javascript"></script>
        <script src="lib/MyLib.js" type="text/javascript"></script>
        <script>
            $(function(){
                $('#signinForm').validate({
                    rules: {
                        mail: {
                            required: true,
                            regex: /^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/i,
                        },
                        password: {
                            required: true,
                            minlength: 8,
                            regex: [/[A-Z]/, /\d/, /['";:,.\/?\\-]/],
                        }
                    },
                    messages: {
                        mail: {
                            required: 'required',
                            regex: 'bad format for email',
                        },
                        password: {
                            required: 'required',
                            minlength: 'minimum 8 characters',
                            regex: 'bad password format',
                        }
                    }
                });
                $("input:text:first").focus();
            });
        </script>
    </head>
    <body>
        <?php
            $menu_title = "";
            $menu_subtitle = "";
            include("menu.php");
        ?>
        <div class="content container">
            <div class="row">
                <div class="col-sm-3"></div>
                <div class="col-sm-6 form">
                    <h2>Sign in</h2>
                    <hr>
                    <form action="user/login" method="post" id="signinForm">
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
