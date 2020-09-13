<?php
use \Blog\Config\Config;
use \Blog\Frontend\Web\SiteConfig;
?>
<header class="open notransition">
	<span class="logo" role="banner"><?= SiteConfig::TITLE ?></span>
	<nav>
		<ul>
			<a href="<?= Config::SERVER_URL ?>/"><li>Startseite</li></a>
			<a href="<?= Config::SERVER_URL ?>/posts/"><li>Artikel</li></a>
		</ul>
	</nav>
	<div class="opener">
		<button title="Navigation anzeigen oder ausblenden">
			<div class="top"></div>
			<div class="middle"></div>
			<div class="bottom"></div>
		</button>
	</div>
</header>