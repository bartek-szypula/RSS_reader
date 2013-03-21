<?php

/*
 * Klasa obsługująca kanał RSS
 */
class RSS {

	private $rss;

	public $channelTitle; 			// Tytuł kanału
	public $channelLink;			// Link do strony
	public $channelDescription;		// Opis kanału
	private $items;					// Obiekt zawierający artykuły z RRS'a		

	private $pageContent;			// Wyświetlane artykuły (tablica)

	function connect($_rssUrl)
	{
		return (@$this->rss = simplexml_load_file($_rssUrl)) ? true : false;
	}

	/**
	 * Metoda uzupełnianiająca pola o dane z obiektu XML
	 */
	function assign()
	{
		$this->channelTitle 	  = $this->rss->channel->title;
		$this->channelLink 		  = $this->rss->channel->link;
		$this->channelDescription = $this->rss->channel->description;

		$this->items = $this->rss->channel->item;
	}

	/**
	 * Metoda wyszukująca słowa
	 */
	function search($word = null, $searchIn = 'description') // $word - wyszukiwanie słowo (jeśli null-wyświetli wszystko), $searchIn - "szukaj w" ("title" / "description")
	{
		$this->pageContent = array();

		foreach($this->items as $row)
		{
			$title 		 = (string)$row->title;
			$description = (string)$row->description;

			if($word != null) // wpisano słowo do wyszukiwarki?
			{
				if(!strstr($$searchIn, $word))
					continue; // jeśli nie znajdzie słowa w artykule, przechodzi do następnego

				$$searchIn = str_replace($word, '<span class="mark">'.$word.'</span>', $$searchIn); // wyróżnianie znalezionego wyrazu
			}

			$article = new PageContent(); // Tworzenie obiektu artykułu

			/*
			 * Uzupełnianie pól artykułu
			 */
			$article->title = $title;
			$article->description = $description;
			$article->link = $row->link;
			$article->commentsUrl = $row->comments;
			$article->pubDate = date("Y-m-d H:i", strtotime($row->pubDate));
			// <dc:creator> 
			$namespaces = $row->getNameSpaces(true);
			$dc = $row->children($namespaces['dc']); 
			$article->creator = $dc->creator;
			// <category>
			$tempArray = array();
			foreach($row->category as $tag):
				array_push($tempArray, (string)$tag); 	// Objekt XML do tablicy
			endforeach;
			$article->tags = implode(', ', $tempArray);		// Tablica na stringa

			array_push($this->pageContent, $article); // Dodawanie artykułu do tablicy artykułów
		}
	}

	/**
	 *	Metoda zwracająca tablicą wyszukanych artykułów
	 */
	function returnPageContent()
	{
		return $this->pageContent;
	}
}