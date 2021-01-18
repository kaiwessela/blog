<?php include COMPONENT_PATH . 'admin/start.php'; ?>
<main>
	<?php
	$Controller = $EventController;
	$Object = $Event;
	$singular = 'Veranstaltung';
	$plural = 'Veranstaltungen';
	$urlclass = 'events';

	include COMPONENT_PATH . 'admin/common-1.php';
	?>


<?php if($EventController->request->action == 'list' && $EventController->found()){ ?>
	<?php
	$pagination = $EventController->pagination;
	include COMPONENT_PATH . 'admin/pagination.php';
	?>

	<?php foreach($Event as $obj){ ?>
	<article>
		<code><?= $obj->longid ?></code>
		<h2><?= $obj->title ?></h2>
		<small><?= $obj->location ?></small>
		<small><?= $obj->timestamp->datetime_long ?></small>
		<div>
			<a class="button blue"
				href="<?= $server->url ?>/admin/events/<?= $obj->id ?>">Ansehen</a>
			<a class="button yellow"
				href="<?= $server->url ?>/admin/events/<?= $obj->id ?>/edit">Bearbeiten</a>
			<a class="button red"
				href="<?= $server->url ?>/admin/events/<?= $obj->id ?>/delete">Löschen</a>
		</div>
	</article>
	<?php } ?>
<?php } ?>

<?php if($EventController->request->action == 'show' && $EventController->found()){ ?>
	<?php $obj = $Event; ?>
	<article>
		<code><?= $obj->longid ?></code>
		<h1><?= $obj->title ?></h1>
		<p><?= $obj->timestamp->datetime_long ?></p>
		<p><?= $obj->location ?></p>
	</article>
<?php } ?>

<?php if(($EventController->request->action == 'edit' && !$EventController->edited()) || ($EventController->request->action == 'new' && !$EventController->created())){ ?>
	<?php $obj = $Event; ?>
	<form action="#" method="post">

		<?php if($EventController->request->action == 'new'){ ?>
		<label for="longid">
			<span class="name">Veranstaltungs-ID</span>
			<span class="conditions">
				erforderlich; 9 bis 60 Zeichen, nur Kleinbuchstaben (a-z), Ziffern (0-9) und
				Bindestriche (-)
			</span>
			<span class="infos">
				Die Veranstaltungs-ID wird in der URL verwendet und entspricht meistens dem Titel.
			</span>
		</label>
		<input type="text" id="longid" name="longid" value="<?= $obj->longid ?>" required size="40" minlength="9" maxlength="60" pattern="^[a-z0-9-]*$" autocomplete="off">
		<?php } else { ?>
		<input type="hidden" name="id" value="<?= $obj->id ?>">
		<input type="hidden" name="longid" value="<?= $obj->longid ?>">
		<?php } ?>

		<label for="title">
			<span class="name">Titel</span>
			<span class="conditions">erforderlich, 1 bis 50 Zeichen</span>
			<span class="infos">Der Titel der Veranstaltung.</span>
		</label>
		<input type="text" id="title" name="title" value="<?= $obj->title ?>" required size="40" maxlength="50">

		<label for="organisation">
			<span class="name">Organisation</span>
			<span class="conditions">erforderlich, 1 bis 40 Zeichen</span>
			<span class="infos">Die Organisation, die zur Veranstaltung eingeladen hat.</span>
		</label>
		<input type="text" id="organisation" name="organisation" value="<?= $obj->organisation ?>" required size="30" maxlength="40">

		<label for="timeinput">
			<span class="name">Datum und Uhrzeit</span>
			<span class="conditions">erforderlich</span>
			<span class="infos">Datum und Uhrzeit der Veranstaltung.</span>
		</label>
		<input type="number" class="timeinput" id="timestamp" name="timestamp" value="<?= $obj->timestamp ?>" required size="10">

		<label for="location">
			<span class="name">Ort</span>
			<span class="conditions">optional, bis zu 60 Zeichen</span>
			<span class="infos">Der Ort der Veranstaltung.</span>
		</label>
		<input type="text" id="location" name="location" value="<?= $obj->location ?>" size="40" maxlength="60">

		<label for="description">
			<span class="name">Beschreibung</span>
			<span class="conditions">optional</span>
			<span class="infos">Beschreibung der Veranstaltung.</span>
		</label>
		<textarea id="description" name="description" rows="5" cols="60"><?= $obj->description ?></textarea>

		<label for="cancelled">
			<span class="name">Absage</span>
			<span class="conditions">optional</span>
			<span class="description">Ist die Veranstaltung abgesagt?
		</label>
		<label class="checkbodge turn-around">
			<span class="label-field">Ja</span>
			<input type="checkbox" id="cancelled" name="cancelled" value="true" <?php if($obj->cancelled){ echo 'checked'; } ?>>
			<span class="bodgecheckbox">
				<span class="bodgetick">
					<span class="bodgetick-down"></span>
					<span class="bodgetick-up"></span>
				</span>
			</span>
		</label>

		<button type="submit" class="green">Speichern</button>
	</form>
<?php } ?>

<?php if($EventController->request->action == 'delete' && !$EventController->deleted()){ ?>
	<?php $obj = $Event; ?>
	<p>Veranstaltung <code><?= $obj->longid ?></code> löschen?</p>
	<form action="#" method="post">
		<input type="hidden" id="id" name="id" value="<?= $obj->id ?>">
		<button type="submit" class="red">Löschen</button>
	</form>
<?php } ?>

</main>

		<?php if($EventController->request->action == 'new' || $EventController->request->action == 'edit'){
			include COMPONENT_PATH . 'admin/timeinput.php';
		} ?>

<?php include COMPONENT_PATH . 'admin/end.php'; ?>