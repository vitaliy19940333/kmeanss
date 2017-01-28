<?php
require_once "config.php";
?>
<html>
	<head>
		<title><?=strip_tags($bred_crumb[max(array_keys($bred_crumb))]['lang_'.$_SESSION['lang']]." | ".$title_site)?></title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="keywords" content="Одесская Националькая академия пищевых технологий : Факультеты, кафедры, центры : Кафедра автоматизации производственных процессов (АПП) : , Образование, Наука, Учеба"/>
		<meta name="description" content="Одесская Националькая академия пищевых технологий : Факультеты, кафедры, центры : Кафедра автоматизации производственных процессов (АПП) : ,  высшее бразование, наука, учеба, Украина, Одесса"/>
		<link rel="stylesheet" href="/css/bootstrap-theme.min.css">
		<link rel="stylesheet" href="/css/bootstrap.css">
		<link rel="stylesheet" href="/css/bootstrap-theme.min.css">
		<link rel="stylesheet" href="/css/jquery.fancybox.css">
		<link rel="stylesheet" href="/css/font-awesome/css/font-awesome.min.css">
		<link rel="stylesheet" href="/css/style.css">
		<style>
		.panel {
	padding: 20px;
	width: 350px;
	background: #eeeeee;
	border: #282828 2px solid;
	font-family: Georgia;
}
		</style>
		<!--<script type='text/javascript' src='/js/jquery.js'></script>-->
		<script type='text/javascript' src='/js/jquery.js'></script>
		<script type="text/javascript" src="/js/bootstrap.min.js"></script>
		<script type='text/javascript' src='/js/jquery.fancybox.pack.js'></script>
		<script src="/js/jquery.tabSlideOut.v1.1.js"></script>
		<script type="text/javascript">
$(function(){
	$('.panel').tabSlideOut({							//Класс панели
		tabHandle: '.handle',						//Класс кнопки
		pathToTabImage: '/images/serring.png',				//Путь к изображению кнопки
		imageHeight: '322px',						//Высота кнопки
		imageWidth: '40px',						//Ширина кнопки
		tabLocation: 'left',						//Расположение панели top - выдвигается сверху, right - выдвигается справа, bottom - выдвигается снизу, left - выдвигается слева
		speed: 300,								//Скорость анимации
		action: 'click',								//Метод показа click - выдвигается по клику на кнопку, hover - выдвигается при наведении курсора
		topPos: '100px',							//Отступ сверху
		fixedPosition: false						//Позиционирование блока false - position: absolute, true - position: fixed
	});
});
</script>
	</head>
<body id="page-top" class="index">

    <!-- Navigation -->
    <nav id="mainNav" class="navbar navbar-default navbar-fixed-top navbar-custom">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header page-scroll">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span> Menu <i class="fa fa-bars"></i>
                </button>
                <a class="navbar-brand" href="#page-top" style='font-family:zhybor'>Zhy<span style='font-family:georgia;color: #97f1ab;'>&</span> Bor</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav navbar-right">
                    <li class="hidden">
                        <a href="#page-top"></a>
                    </li>
                    <li class="page-scroll">
                        <a href="/home"><i class="fa fa-home" aria-hidden="true"></i> Главная</a>
                    </li>
                    <li class="page-scroll">
                        <a href="/insert"><i class="fa fa-cloud-download" aria-hidden="true"></i> Добавить данные</a>
                    </li>
                    <li class="page-scroll">
                        <a href="/help"><i class="fa fa-question-circle" aria-hidden="true"></i> Помощь</a>
                    </li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container-fluid -->
    </nav>

   <Style>
	ul li{list-style-type:none;font-size:12px;}
	ul, ol {
    margin-top: 0;
    margin-bottom: 10px;
    margin-left: -35px !important;
	}
	#setting p {
		font-size:12px;
	}
	#val_hands{
		display: inline-block;
		max-width: 50px;
		max-height: 30px !important;
		text-align: center;
	}
	#auto{
		clear:both;
	}
	#setting p {
    font-size: 12px;
    background: #2c3e50;
    padding: 4px;
    text-align: center;
    color: #fff;
    box-shadow: 2px -6px 2px -1px #2c3e50;
}
	
   </style>
    <div class='main' style='margin-top:120px;'>
	<div class="panel" style='z-index:999;'>
	<a class="handle" href="http://jemand.ru" style='display:none'> </a> <!-- Ссылка для пользователей с отключенным JavaScript -->

	<span lang="ru">
		<div class='col-md-12 ' style="     box-shadow: 0 0 10px 5px rgb(255, 255, 255);
    margin-bottom: 10px;
    background: #fff;">
					<?php include "setting_block.php" ?>
				</div>
	</span>
</div>


		<div class='container'>
			<div class='row'>
				<div class='col-md-12' style=" box-shadow: 0 0 10px 5px rgba(221, 221, 221, 1);margin-bottom: 10px;">
					<ul id="breadcrumbs-one">
						<?php foreach($bred_crumb as $n => $crumb):?>
						<li><a href='/<?=$bred_crumb[0]['link']."/".$bred_crumb[1]['link']?>'><?=$crumb['lang_'.$_SESSION['lang']]?></a></li>
						<?php endforeach?>
					</ul>
					<?php include $content_view?>
				</div>
			</div>
		</div>
	</div>
	<style>
	.col-md-3 {
    width: 24%;
    margin-right: 9px;
}
	</style>

    <!-- Footer -->
    <footer class="text-center">
        <div class="footer-above">
            <div class="container">
                <div class="row">
                    <div class="footer-col col-md-4">
                        <!--<h3>ONAFT</h3>-->
                        <a href="http://onaft.edu.ua/" target="_blank"><p><img src='/images/logo10.png' height="140px">
                            <br>Одесская Национальная Академия Пищевых Технологий</p></a>
                    </div>
                    <div class="footer-col col-md-5">
                        <!--<h3>ONAFT</h3>-->
                      <a href="http://aip.onaft.edu.ua/"  target="_blank"> <p><img src='/images/midsize-221.png' height="140px">
                            <br>Кафедра Автоматизации Технологических 
								Процессов и Робототехнических Систем</p></a>
                    </div>
                    <div class="footer-col col-md-3" style='text-align:left'>
                        <h3 style='font-size:16px'>Разработчики модуля:</h3>
						<a href="http://www.aip.onaft.edu.ua/about/consist/view/13" target="_blank"><p><i class="fa fa-user" aria-hidden="true"></i> Жигайло Алексей Михайлович</p></a>
						<a href=""><p><i class="fa fa-user" aria-hidden="true"></i> Борис Виталий Васильевич</p></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-below">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        Copyright &copy; Odessa National Academy of Food Technologies 2016
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Button (Only visible on small and extra-small screen sizes) 
    <div class="scroll-top page-scroll">
        <a class="btn btn-primary" href="#page-top">
            <i class="fa fa-chevron-up"></i>
        </a>
    </div>-->

  
   
    
    <!-- jQuery -->
    <!-- Bootstrap Core JavaScript -->
    <script src="/js/vendor/bootstrap/js/bootstrap.min.js"></script>
    <!-- Theme JavaScript -->
    <script src="/js/freelancer.min.js"></script>
</body>
</html>