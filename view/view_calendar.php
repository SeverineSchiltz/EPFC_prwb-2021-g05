<!DOCTYPE html>
<html lang='en'>
  <head>
    <meta charset="UTF-8">
    <title>Calendar</title>
    <base href="<?= $web_root ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="css/calendar.css" rel="stylesheet" type="text/css"/>
    <link href="css/style.css" rel="stylesheet" type="text/css"/>
    <link href="css/menu.css" rel="stylesheet" type="text/css"/>
    <script src="lib/jquery-3.6.0.min.js" type="text/javascript"></script>
    <script src="lib/jquery-validation-1.19.3/jquery.validate.min.js" type="text/javascript"></script>
    <link href='lib/FullCalendar/main.css' rel='stylesheet' />
    <script src='lib/FullCalendar/main.js'></script>
    <script>
      var boards = <?= $boards_json ?>;
      var calendar;
      var events=new Array();
      var boardCheck;
      $(function(){
        boardCheck = $('#boardCheck');

        //créer les events sur base des cartes
        var i = 0;
        for (var b of boards) {
          for (var c of b.cards) {
            var event = {
              id: c.card_id,
              title: c.card_title,
              start: c.card_due_date,
              backgroundColor: b.color,
              borderColor: new Date(c.card_due_date) <= new Date(Date.now()) ? "red" : "black",
              className: new Date(c.card_due_date) <= new Date(Date.now())  ? "redBorder" : "blackBorder", //new Date().toISOString().slice(0,10));
              textColor: 'white',
            }
            console.log(event);
            events[i]= event;
            ++i;
          }
        }

        var calendarEl = document.getElementById('calendar');
        calendar = new FullCalendar.Calendar(calendarEl, {
          initialView: 'dayGridMonth',
          headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek'
          },
          events
        });
        calendar.render();

        displayCheckboxBoard();

      });

      function toggleCards(idBoard) {
        var checkBoard = document.getElementById(idBoard);
        if(checkBoard.checked) {
          console.log('You showed ' + idBoard);
          //showCards(idBoard);
          //pour ajouter
          /*
          calendar.addEvent(
            {
              id: 2,
              title: 'Event1',
              start: '2021-05-04',
              color: 'yellow',   // an option!
              textColor: 'black' // an option!
            }
          ); */
        }
        else {
          console.log('You hid ' + idBoard);
          //hideCards(idBoard);
          //Pour deleter:
          //var event = calendar.getEventById(1); //attention, ici faut mettre l'id de la carte
          //event.remove();
        }
      }

      function displayCheckboxBoard(){
        var htmlMyBoard = "";
        var htmlOtherBoard = "";
        var htmlNotSharedBoard = "";
          for (var b of boards) {
              if(b.board_type === "my_boards"){
                htmlMyBoard += "<input type='checkbox' id='" + b.board_id + "' onclick='toggleCards(" + b.board_id + ")' checked>";
                htmlMyBoard += "<label style='color: " + b.color + ";'>&ensp;" + b.board_title + "&ensp;&ensp;</label> ";
              }else if(b.board_type === "other_boards"){
                htmlOtherBoard += "<input type='checkbox' id='" + b.board_id + "' onclick='toggleCards(" + b.board_id + ")' checked>";
                htmlOtherBoard += "<label style='color: " + b.color + ";'>&ensp;" + b.board_title + "&ensp;&ensp;</label> ";
              }else if(b.board_type === "not_shared_boards"){
                htmlNotSharedBoard += "<input type='checkbox' id='" + b.board_id + "' onclick='toggleCards(" + b.board_id + ")' checked>";
                htmlNotSharedBoard += "<label style='color: " + b.color + ";'>&ensp;" + b.board_title + "&ensp;&ensp;</label> ";
              }
          }
          if(htmlMyBoard !== ""){
            htmlMyBoard = "<h3>Your boards</h3>" + htmlMyBoard;
          }
          if(htmlOtherBoard !== ""){
            htmlOtherBoard = "<h3>Other boards</h3>" + htmlOtherBoard;
          }
          if(htmlNotSharedBoard !== ""){
            htmlNotSharedBoard = "<h3>Not shared boards</h3>" + htmlNotSharedBoard;
          }
          boardCheck.html(htmlMyBoard + htmlOtherBoard + htmlNotSharedBoard);
      }

    </script>
  </head>
  <body>
    <?php
          $menu_title = "";
          $menu_subtitle = "Boards";
          include("menu.php");
    ?>
    <div class="content">
      <div id='boardCheck'></div>
      <br />
      <div id='calendar'></div>
      <noscript>
        Your browser does not support JavaScript!
      </noscript>
    </div>
  </body>
</html>