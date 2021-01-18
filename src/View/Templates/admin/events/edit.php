<form action="#" method="post">

<?php if($EventController->request->action == 'new'){ ?>

	<!-- LONGID -->
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
	<input type="text" size="40" autocomplete="off"
		id="longid" name="longid" value="<?= $Event->longid ?>"
		minlength="9" maxlength="60" pattern="^[a-z0-9-]*$" required>

<?php } else { ?>

	<input type="hidden" name="id" value="<?= $Event->id ?>">
	<input type="hidden" name="longid" value="<?= $Event->longid ?>">

<?php } ?>

	<!-- TITLE -->
	<label for="title">
		<span class="name">Titel</span>
		<span class="conditions">erforderlich, 1 bis 50 Zeichen</span>
		<span class="infos">Der Titel der Veranstaltung.</span>
	</label>
	<input type="text" size="40"
		id="title" name="title" value="<?= $Event->title ?>"
		maxlength="50" required>

	<!-- ORGANISATION -->
	<label for="organisation">
		<span class="name">Organisation</span>
		<span class="conditions">erforderlich, 1 bis 40 Zeichen</span>
		<span class="infos">Die Organisation, die zur Veranstaltung eingeladen hat.</span>
	</label>
	<input type="text" size="30"
		id="organisation" name="organisation" value="<?= $Event->organisation ?>"
		maxlength="40" required>

	<!-- TIMESTAMP -->
	<label for="timeinput">
		<span class="name">Datum und Uhrzeit</span>
		<span class="conditions">erforderlich</span>
		<span class="infos">Datum und Uhrzeit der Veranstaltung.</span>
	</label>
	<input type="number" size="10" class="timeinput"
		id="timestamp" name="timestamp" value="<?= $Event->timestamp ?>" required>

	<!-- LOCATION -->
	<label for="location">
		<span class="name">Ort</span>
		<span class="conditions">optional, bis zu 60 Zeichen</span>
		<span class="infos">Der Ort der Veranstaltung.</span>
	</label>
	<input type="text" size="40"
		id="location" name="location" value="<?= $Event->location ?>"
		maxlength="60">

	<!-- DESCRIPTION -->
	<label for="description">
		<span class="name">Beschreibung</span>
		<span class="conditions">optional</span>
		<span class="infos">Beschreibung der Veranstaltung.</span>
	</label>
	<textarea id="description" name="description"
		cols="60" rows="5">
		<?= $Event->description ?>
	</textarea>

	<!-- CANCELLED -->
	<label for="cancelled">
		<span class="name">Absage</span>
		<span class="conditions">optional</span>
		<span class="description">Ist die Veranstaltung abgesagt?
	</label>
	<label class="checkbodge turn-around">
		<span class="label-field">Ja</span>
		<input type="checkbox" id="cancelled" name="cancelled" value="true"
			<?php if($Event->cancelled){ echo 'checked'; } ?>>
		<span class="bodgecheckbox">
			<span class="bodgetick">
				<span class="bodgetick-down"></span>
				<span class="bodgetick-up"></span>
			</span>
		</span>
	</label>

	<button type="submit" class="green">Speichern</button>
</form>
