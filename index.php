<!doctype html>
<html>
<head>
	<meta name="author" content="Bartłomiej Szypuła" />
	<meta name="contact" content="bartek.szypula@gmail.com" />
	<meta charset="UTF-8" />
	<title>xLab.pl - RSS Reader - Bartłomiej Szypuła</title>
	<link rel="stylesheet" href="style.css" />
</head>
<body>

<!-- POST/GET -->
<?php
	$word = 'Symfony'; $searchIn = 'description'; // domyślne ustawienie

	if(isset($_POST['word']) && isset($_POST['searchIn'])) {
		$word = $_POST['word'];
		$searchIn = $_POST['searchIn'];
		if($searchIn != 'title' && $searchIn != 'description')
			$searchIn = 'description';
	} else if (isset($_GET['word']) && isset($_GET['searchIn'])) {
		$word = $_GET['word'];
		$searchIn = $_GET['searchIn'];
		if($searchIn != 'title' && $searchIn != 'description')
			$searchIn = 'description';
	}
?>

	<div class="container">
		<form action="index.php" method="POST">
			<div class="row">
				<label>Wyszukaj słowa:</label>
				<input type="text" name="word" value=<?php echo"\"$word\"";?> />
			</div>
			<div class="row">
				<label>Wyszukuj w:</label>
				<select name="searchIn">
					<option value="title" <?php if($searchIn == 'title') echo 'selected'; ?>>Tytule</option>
					<option value="description" <?php if($searchIn == 'description') echo 'selected'; ?>>Opisie</option>
				</select>
			</div>
			<div class="row">
				<input type="submit" value="Szukaj" />
			</div>
		</form>
		<p class="italic">ew. index.php?word=Symfony&searchIn=description</p>

<?php
	function __autoload($_class)
	{
		require_once('class_'.$_class.'.php');
	}

	$rssUrl = "http://xlab.pl/feed/";
	//$rssUrl = "xlab.xml";

	$RSS = new RSS(); // Tworzenie obiektu klasy obsługijącej wyszukiwarkę RSS

	if($RSS->connect($rssUrl))
	{
		$RSS->assign(); // Uzupełnianie klasy o dane z obiektu XML

		echo '<header>
			  <h1><a href="'.$RSS->channelLink.'" target="_blank">'.$RSS->channelTitle.'</a></h1>
			  <h4>'.$RSS->channelLink.'</h4>
			  <h2>'.$RSS->channelDescription.'</h2></header>';

		$RSS->search($word, $searchIn); // szukane słowo, zakres wyszukiwania ("title" / "description")
		// $RSS->search(); // wyświetlanie wszystkich artykułów

		if(count($RSS->returnPageContent()) == 0):
			echo '<h3 class="error">Nie znaleziono żadnego artykułu - wpisz inne słowo w wyszukiwarce.</h3>';
		else:
			foreach($RSS->returnPageContent() as $row) // tablica zawierająca aktykuły
			{
				echo '<article>
					  <div class="title"><h3><a href="'.$row->link.'">'.$row->title.'</a></h3></div>
					  <span>('.$row->pubDate.')</span> - 
					  <span>'.$row->creator.'</span>
					  <p>'.$row->description.'</p>
					  <p><span class="bold">Tagi:</span> '.$row->tags.'</p></article>';
			}
		endif;
	}
	else
		echo 'Błędny adres/plik RSS!';
?>

	</div>
</body>
</html>