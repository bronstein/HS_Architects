<?
	// general vars settings
	$like_href = "#";
	$msg_box_class = "";
	$error_msg = array();
	$msg_title = "";
	$action = htmlspecialchars($_SERVER['PHP_SELF'])."#contact_form";
	$name_value  = "";
	$phone_vale  = "";
	$email_value = "";

// the form has submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

	// form handling vars
	$email_to = "bronstein@gmail.com";
  $email_subject = "אורח באתר יצר קשר";

	// input class
	class formElement {
		
		public $content = null;
		public $type    = null;
		public $valid   = false;
		public $present = false;
	
		// constractor
		public function  __construct($type1, $content1) {
			$this->content = $content1;
			$this->type    = $type1;
			$this->present = $this->isPresent();
			$this->valid   = $this->present ? $this->validateElement() : false;
		}

		// check if empty
		private function isPresent() {
			$tmp = str_replace(' ', '', $this->content);
			if(!empty($tmp)) return true;
		}
	
		// validates
		private function validateElement() {
			switch($this->type) {
	      case 'name':
	          if(!ctype_alpha($this->content)) {
	              return FALSE;
	          }
	      break;

	      case 'phone':
						$allow = array(' ', '-');
						$tmpp = str_replace($allow, '', $this->content);
	          if(!ctype_digit($tmpp)) {
	              return FALSE;
	          }
	      break;

	      case 'email':
	          if(!filter_var($this->content, FILTER_VALIDATE_EMAIL)) {
	              return FALSE;
	          }
	      break;	 
			  }
			return true;
		}
	}

	//collect error messages
	function findErrors($name, $phone, $email) {
		global $error_msg, $msg_title;
		$no_errors = true;
		$msg_text = "נא לתקן את השדות הבאים:";
		if(!$name->present) {
			array_push($error_msg, "חובה למלא שם");
			$no_errors = false;
		}
		if(!$phone->present && !$email->present) {
			array_push($error_msg, "חובה למלא טלפון או מייל");
			$no_errors = false;
		}
		if(!$name->valid && $name->present) {
			array_push($error_msg, "השם אינו תקין");
			$no_errors = false;
		}
		if(!$phone->valid && $phone->present) {
			array_push($error_msg, "המספר אינו תקין");
			$no_errors = false;
		}
		if(!$email->valid && $email->present) {
			array_push($error_msg, "הכתובת אינה תקינה");
			$no_errors = false;
		}
		$msg_title = $no_errors ? "" : $msg_text;
		return $no_errors;

	}

	function sendEmail($name, $phone, $email) {
		global $email_to, $email_subject, $error_msg, $msg_title;
		$from = "hayaShachamArquitects.com";
		$headers = "From: $from";
		$message = "מחפשים אותך דרך האתר"."\n";
		$message .= "שם:".$name->content."\n";
		$message .= "טלפון:".$phone->content."\n";
		$message .= "מייל:".$email->content."\n";
		$mailsent = mail($email_to, $email_subject, $message, $headers);
		if (!$mailsent) {
			$msg_title = "ההודעה לא נשלחה!";
			array_push($error_msg, "אנא צרו קשר בטלפון או במייל");
		}
		else{
			$msg_title = "ההודעה נשלחה בהצלחה!";
		}
	}

	function setResultArray() {	//only for AJAX
		global $error_msg, $msg_title;
		$status = empty( $error_msg ) ? "green" : "red";
		$arr = array("status" => $status, "title" => $msg_title, "messages" => $error_msg);
		return $arr;
	}
		
	function printFormResult() {  //only for NonAJAX
		global $error_msg, $msg_box_class, $msg_title;
		if ( empty( $error_msg )) {
			$msg_box_class = "green";
		}
		else {
			$msg_box_class = "red";
		}
	}

	$name_tmp  = isset($_POST['user_name']) ? htmlspecialchars($_POST['user_name']) : "";
	$phone_tmp = isset($_POST['phone_no'])  ? htmlspecialchars($_POST['phone_no'])  : "";
	$email_tmp = isset($_POST['email'])     ? htmlspecialchars($_POST['email'])     : "";

	$user_name  = new formElement("name",   $name_tmp);
	$phone_no   = new formElement("phone",  $phone_tmp);
	$email_from = new formElement("email",  $email_tmp);

	// look for errors and try to send email
	if(findErrors($user_name, $phone_no, $email_from)) {
		sendEmail($user_name, $phone_no, $email_from);
	}
	else { // keep input values for NonAJAX
		$name_value = $user_name->content;
		$phone_value = $user_phone->content;
		$email_value = $email_name->content;
	}
	
	// submitted via AJAX
	if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
		die(json_encode(setResultArray()));
	}

	// submitted with JS disabled
	else {
		printFormResult();
	}
}

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no"">
    <meta name="description" content="האתר הרשמי של חיה שחם, אדריכלית">
    <meta name="author" content="חיה שחם">
		
    <title>חיה שחם - אדריכלית</title>
		
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.1/css/bootstrap.min.css">
		<link rel="stylesheet/less" type="text/css" href="style/hstyle.less">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

		
  </head>

  <body id="mybody" data-spy="scroll" data-target="#main_menu_navbar" >
		
		<div class="h_header_div">
			<div class="container">
		 		<div class="header_txt">חיה שחם / אדריכלית</div>
				<? include 'partials/contact_menu.php' ?>
			</div>
		</div>

		<img src="/style/images/header_pic.png" alt="חיה שחם אדריכלית" class="img-responsive">

		<!-- navbar -->
		<div id="main_menu_wrap" class="static_menu_bar">
			<div class="container main_menu_container">
				<nav class="navbar h-navbar" role="navigation">
					<div class="navbar-header navbar-right">
					  <a class="navbar-brand" href="#">חיה שחם אדריכלית</a>
					</div>
					<div id="main_menu_navbar">
					 	<ul class="nav navbar-nav">		
					    <li class='main_menu_element'><a href="#contact" data-target="" >צור קשר</a></li>
					    <li class='main_menu_element'><a href="#articles">מאמרים</a></li>
					    <li class='main_menu_element'><a href="#gallery">גלריה</a></li>
							<li class="main_menu_element active"><a href="#about">אודות</a></li>					
				 		</ul>
					</div>
					<? include 'partials/contact_menu.php' ?>
				</nav>
			</div>
		</div>
		<!-- end of navbar -->

		<section id="about">
		  <div class="container">
				<div class="container home_page">
					<div class="row">
						<div class="col-lg-6 col-lg-offset-6 col-md-8 col-md-offset-4">
							<p class="page_headline home_title">ליווי צמוד משלב התכנון ועד הכנסת השטיחון לכניסת הבית</p>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-5 col-lg-offset-7 col-md-6 col-md-offset-6">
							<p class='page_headline'>חיה שחם אדריכלית מוסמכת, מתמחה באדריכלות ועיצוב פנים לבתים פרטיים ולמבני ציבור.</p>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-9 col-lg-offset-3 col-md-10 col-md-offset-2">
							<p class='page_headline'>אדריכלות יצירתית ולא שגרתית ששמה דגש על בניה ירוקה המתאימה את הבית לסביבתו הטבעית מבלי להתפשר על העיצוב.</p>
						</div>
					</div>
		    </div>
			</section>

			<!-- gallery -->
			<section id="gallery">
				<div class="container">
		  		<div id='gallery_line' class='hor_strip_box'><img src='style/images/hand_line.png'></div>
				</div>

				<div class="container">
					<div id="h-carousel" class="carousel slide" data-ride="carousel">
				<!-- Indicators -->
						<ol class="carousel-indicators">
						  <li data-target="#h-carousel" data-slide-to="0" class="active"></li>
						  <li data-target="#h-carousel" data-slide-to="1"></li>
						  <li data-target="#h-carousel" data-slide-to="2"></li>
							<li data-target="#h-carousel" data-slide-to="3"></li>
							<li data-target="#h-carousel" data-slide-to="4"></li>
						</ol>

						<!-- Wrapper for slides -->
						<div class="carousel-inner">
						  <div class="item active">
						    <img src="/style/images/gallery_pic_0.jpg" alt="...">
		 						<div class="carousel-caption">
									<p>בית במושב בצפון</p>
								</div>
		    			</div>
							<div class="item">
						    <img src="/style/images/gallery_pic_1.jpg" alt="...">
		 						<div class="carousel-caption">
									<p>בית במושב בדרום</p>
								</div>
		    			</div>
							<div class="item">
						    <img src="/style/images/gallery_pic_2.jpg" alt="...">
		 						<div class="carousel-caption">
									<p>בית במושב אסלה</p>
								</div>
		    			</div>
							<div class="item">
						    <img src="/style/images/gallery_pic_3.jpg" alt="...">
		 						<div class="carousel-caption">
									<p>בית במושב ליצים</p>
								</div>
		    			</div>
							<div class="item">
						    <img src="/style/images/gallery_pic_4.jpg" alt="...">
		 						<div class="carousel-caption">
									<p>בית במושב ניצוצים</p>
								</div>
		    			</div>
		  			</div>

	 					<!-- Controls -->
	 					<a class="left carousel-control" href="#h-carousel" data-slide="prev">
		  				<span class="h-arr h-arr-left"></span>
						</a>
						<a class="right carousel-control" href="#h-carousel" data-slide="next">
		  				<span class="h-arr h-arr-right"></span>
						</a>
					</div>
				</div>
			</section>
			<!-- Articles -->

			<section id="articles">
				<div class="container">
		  		<div id='articles_line' class='hor_strip_box'><img src='style/images/hand_line.png'></div>
				</div>

				<!-- Article 1 -->
				<div class="container h-article">
					<div class="clearfix">
						<div class="article_pic_box">
							<img src="/style/images/article_pic_0.png" alt="...">
						</div>
						<h2>אור וצל</h2>
						<p class="first_p">מאמר שסוקר את האפשרויות הרבות למשחק אור וצל. באלקטרודינאמיקה קוונטית כל חלקיק הוא גם גל, אם כי יש לצפות להתנהגות שונה מקוורקים פרועי שיער.
						</p>
						<a id="open_article_btn1" class="toggle_article_btn open_article_btn" href="#" data-open_btn="#open_article_btn1" data-open_target="#h_collapse1">
					    המשך המאמר...
					  </a>
						<a class="like_article_btn" href="<? echo $like_href; ?>">
					  </a>
					</div>
					<div id="h_collapse1" class="h_collapse">
						<p>אור יכול להגיע לאיזור הצל ממקורות אחרים, או מהמקור הראשי באופן עקיף, דרך החזרה או עקיפה. אם לא מגיע מספיק אור מהכיוון הכללי שלשמו נוצרה מכונת השנים, אזי נוכל לראות את צל צילו של אותו בעל הצל כאילו שהפיץ בעצמו את הסתרת אור השמש.
						</p>
						<p class="last_p">הטרודינאמית איננה מחלת מין במובן המקובל. הפרסים הקדומים הבחינו בין שלושה סוגים שונים של הטרודינאמיות: אוזבקית, נפרוטיטית ומיצרו-אוסטרית. יחד עם זאת, חשוב לקחת בחשבון את ההבחנה המודרנית בין ההטרו לדינאמי שאיננה מובנית מאליה.
						</p>
						<a class="toggle_article_btn close_article_btn" href="#" data-open_btn="#open_article_btn1" data-open_target="#h_collapse1">
					    סגור
					  </a>
						<a class="like_article_btn lower_like_btn" href="<? echo $like_href; ?>"></a>
					</div>
				</div>

				<div class="container">
					<div id='article2_line' class='hor_strip_box'><img src='style/images/hand_line.png'></div>
				</div>

				<!-- Article 2 -->
				<div class="container h-article">
					<div class="clearfix">
						<div class="article_pic_box">
							<img src="/style/images/article_pic_1.png" alt="...">
						</div>
						<h2>הסימטריה ושבירתה</h2>
						<p class="first_p">סימטריה היא תכונה של עצמים מתמטיים ולא דוקא מתמטיים. עצם הוא סימטרי ביחס לטרנספורמציה כלשהי אם ורק אם ניתן להוכיח את קיומות הבלתי מתפשר של אותו עצם. מן הפן השני, נראה שהציפיות עבור תפקודה של מערכת רכיבים כזו אינם עולים בקנה אחד עם דרישות השוק. בשורה התחתונה ניתן לראות שורה שאין אחריה שורות נוספות.
						</p>
						<a id="open_article_btn2" class="toggle_article_btn open_article_btn" href="#" data-open_btn="#open_article_btn2" data-open_target="#h_collapse2">
				    המשך המאמר...
						</a>
						<a class="like_article_btn" href="<? echo $like_href; ?>">
						</a>
					</div>
					<div id="h_collapse2" class="h_collapse">
						<p>אור יכול להגיע לאיזור הצל ממקורות אחרים, או מהמקור הראשי באופן עקיף, דרך החזרה או עקיפה. אם לא מגיע מספיק אור מהכיוון הכללי שלשמו נוצרה מכונת השנים, אזי נוכל לראות את צל צילו של אותו בעל הצל כאילו שהפיץ בעצמו את הסתרת אור השמש.
						</p>
						<p class="last_p">הטרודינאמית איננה מחלת מין במובן המקובל. הפרסים הקדומים הבחינו בין שלושה סוגים שונים של הטרודינאמיות: אוזבקית, נפרוטיטית ומיצרו-אוסטרית. יחד עם זאת, חשוב לקחת בחשבון את ההבחנה המודרנית בין ההטרו לדינאמי שאיננה מובנית מאליה.
						</p>
						<a class="toggle_article_btn close_article_btn" href="#" data-open_btn="#open_article_btn2" data-open_target="#h_collapse2">
				  סגור
						</a>
						<a class="like_article_btn lower_like_btn" href="<? echo $like_href; ?>"></a>
				  </div>
				</div>
			</section>

			<!-- Contact Us -->
			<section id="contact">
				<div class="container">
		  		<div id='contact_line' class='hor_strip_box'><img src='style/images/hand_line.png'></div>
				</div>
			
				<div class="container">
					<p class="contact_header">צרו קשר לפגישת היכרות חינם:</p>
					<div class="contacts_open_details clearfix row">
						<div class="contacts_open_element col-md-4">
							<img src='style/images/ic_phone.png'>
							<span>052-4739323</span>
						</div>
						<div class="contacts_open_element col-md-4">
							<img src='style/images/ic_envelope.png'>
							<a href="mailto:haya@shach.am" class="english">haya@shach.am</a>
						</div>
						<div class="contacts_open_element col-md-4">
							<img src='style/images/ic_f.png'>
							<a href="https://www.facebook.com/HayaShachamArchitects">חיה שחם אדריכלית</a>
						</div>
					</div>
					<div class="container">
		  			<div id='contact_form' class='hor_strip_box'><img src='style/images/hand_line.png'></div>
					</div>
					<p class="contact_header">או מלאו את הפרטים ואחזור אליכם בהקדם:</p>

					<form id="h_form" name="h_form" method="post" action="<? echo $action; ?>" class="form-horizontal clearfix row" role="form">
						<div class="row">
							<div class="h_input col-lg-3">
								<label for="user_name" class="control-label">שם</label>
								<div>
									<input type="text" value="<? echo $name_value; ?>" name="user_name" class="form-control">
								</div>
							</div>
							<div class="h_input col-lg-4">
								<label for="phone_no" class="control-label">טלפון</label>
								<div>
									<input type="text" value="<? echo $phone_value; ?> " name="phone_no" class="form-control">
								</div>
							</div>
							<div class="h_input col-lg-5">
								<label for="email" class="control-label">מייל</label>
								<div>
									<input type="email" value="<? echo $email_value; ?>" name="email" class="form-control english">
								</div>
							</div>
						</div>
						<div class="row submit_row">
							<div class="form_result col-lg-2 <? echo $msg_box_class; ?>">
								<h3><? echo $msg_title; ?></h3>
								<div class="errors">
									<? 	foreach ($error_msg as $value) {
												echo '<div class="single_error">'.$value.'</div>';
											}
									?>
								</div>
							</div>
							<div class="submit_box">
								<button type="submit" data-dismiss="modal" class="btn btn-default">שלח</button>
							</div>
						</div>
					</form>
				</div>
			</section>
		</div>
	
	<!-- Footer -->

		<div class="footer">
			<span>כל הזכויות שמורות לחיה שחם</span>
			<span class="h_slash">&#47;&#47;</span>
			<span>בניית אתר: </span>
			<a href="https://facebook.com/studioeshkat">סטודיו אשקת</a>
		</div>		
    

	<script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
	<script src="/scripts/less.js"></script>
	<script src="/scripts/bootstrap.min.js"></script>
	<script src="/scripts/scripts.js"></script>

  </body>
</html>
