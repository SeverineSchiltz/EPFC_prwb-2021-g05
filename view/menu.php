<div class="menu">
    <div class="title">Trello!</div>
    <div class="spacer"></div>
    <?php if (isset($user) && $user->get_mail()): ?>
        <div class="page-title"><?php echo $menu_title ?></div>
        <div class="right-menu">
            <!-- <div class="page-subtitle"><?php echo $menu_subtitle ?></div> -->
            <a class="page-title" href="board/index">Boards</a>  
            <a class="page-title calendar" href="main/calendar">Calendar</a>  
            <a href="board/index">
                <?php if ($user->is_admin()): ?>
                    <img src="picture/user-shield-solid.svg" alt="">
                <?php else: ?>
                    <i class="fa fa-user"></i>
                <?php endif; ?>
                <span class="username"><?php echo $user->get_full_name() ?></span>
            </a>        
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