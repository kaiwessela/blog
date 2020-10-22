<!DOCTYPE html>
<html lang="de">
	<head>
		<?php include COMPONENT_PATH . 'head.php'; ?>
		<title><?= $Page->object->title ?> – <?= $site->title ?></title>
	</head>
	<body>
		<?php include COMPONENT_PATH . 'header.php'; ?>
		<main>
			<?= $Page->object->content->parsed ?>
		</main>
		<?php include COMPONENT_PATH . 'footer.php'; ?>
		<?php include COMPONENT_PATH . 'scripts.php'; ?>
	</body>
</html>
