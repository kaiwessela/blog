<!DOCTYPE html>
<html lang="de">
	<head>
		<?php include COMPONENT_PATH . 'head.comp.php'; ?>
		<title>Alle Artikel – Kai Florian Wessela</title>
	</head>
	<body>
		<?php include COMPONENT_PATH . 'header.comp.php'; ?>
		<main>
			<section>
				<header class="highlighted">
					<h1>Alle Artikel</h1>
				</header>
				<div>
					<b>Seite <?= $pagination->current_page ?> von <?= $pagination->page_count ?></b>
					– Angezeigt werden Artikel <?= $pagination->get_first_object_number() ?> bis
					<?= $pagination->get_last_object_number() ?> von insgesamt <?= $pagination->object_count ?> Artikeln
				</div>
				<div class="pagination">

<?php
$pagination_items = [
	0 => ['first', 'first-last', 'Erste Seite', 'Erste'],
	1 => ['-10', 'ten', 'Zehn Seiten zurück', '−10'],
	2 => ['-3', '', 'Drei Seiten zurück', '−3'],
	3 => ['-2', '', 'Zwei Seiten zurück', '−2'],
	4 => ['-1', '', 'Vorherige Seite', '−1'],
	5 => ['current', 'current', 'Aktuelle Seite', 'Seite&nbsp;{num}'],
	6 => ['+1', '', 'Nächste Seite', '+1'],
	7 => ['+2', '', 'Zwei Seiten vor', '+2'],
	8 => ['+3', '', 'Drei Seiten vor', '+3'],
	9 => ['+10', 'ten', 'Zehn Seiten vor', '+10'],
	10 => ['last', 'first-last', 'Letzte Seite', 'Letzte']
];

foreach($pagination_items as $pg_item){

					include COMPONENT_PATH . 'pagination-item.comp.php';

}
?>

				</div>

<?php
$error = false;
try {
	$posts = Post::pull_all($pagination->get_object_limit(), $pagination->get_object_offset());
} catch(EmptyResultException $e){
	$error = true;
				?>

				<p>Keine Posts vorhanden.</p>

				<?php
} catch(DatabaseException $e){
	$error = true;
				?>

				<p>Interner Datenbankfehler</p>

				<?php
}

if(!$error){
	foreach($posts as $post){

		include COMPONENT_PATH . 'preview-post.comp.php';

	}
}
?>

			</section>
		</main>
		<?php include COMPONENT_PATH . 'footer.comp.php'; ?>
		<?php include COMPONENT_PATH . 'scripts.comp.php'; ?>
	</body>
</html>
