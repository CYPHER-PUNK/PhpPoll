<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title>Polls</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>body {padding-top: 60px; /* 60px to make the container go all the way to the bottom of the topbar */}</style>
    <link rel="stylesheet" type="text/css" href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.2/css/bootstrap.min.css" />
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
</head>
<body>
    <div class="navbar navbar-inverse navbar-fixed-top">
        <div class="navbar-inner">
            <div class="container">
                <a class="brand" href="/">Polls</a>
                <div class="nav-collapse collapse">
                    <ul class="nav">
                        <li><a href="?action=vote">Голосовать</a></li>
                        <li><a href="?action=create">Создать</a></li>
                        <li><a href="?action=index">Список</a></li>
                    </ul>
                </div><!--/.nav-collapse -->
            </div>
        </div>
    </div>
    <div class="container">
        <?=$this->content?>
    </div> <!-- /container -->
</body>
</html>