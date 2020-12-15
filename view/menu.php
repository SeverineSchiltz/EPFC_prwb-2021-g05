<div class="menu">
    <div class="title">Trello!</div>
    <div class="spacer"></div>
    <?php if (isset($user) && $user->mail): ?>
        <div class="page-title">%TITLE%</div>
        <div class="right-menu">
            <i class="fa fa-user"></i>
            <span class="username"><?php echo $user->full_name ?></span>
            <a class="signout btn" href="main/logout">
                <i class="fa fa-sign-out"></i>
            </a>
        </div>
    <?php else: ?>
    <div class="right-menu">
        <a class="signin btn" href="user/login">
            <i class="fa fa-sign-in"></i>
        </a>
        <a class="signup btn" href="user/signup">
            <i class="fa fa-user-plus"></i>
        </a>
    </div>
    <?php endif; ?>
</div>