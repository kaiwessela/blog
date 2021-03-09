<form action="#" method="post" enctype="multipart/form-data" class="images edit">

<?php if($ApplicationController->request->action == 'new'){ ?>

	<!-- LONGID -->
	<label for="longid">
		<span class="name">Dateiname</span>
		<span class="conditions">
			erforderlich; 9 bis 60 Zeichen, nur Kleinbuchstaben (a-z), Ziffern (0-9) und
			Bindestriche (-)
		</span>
		<span class="infos">
			Der Dateiname wird als Name der Datei verwendet und sollte auf den Inhalt der Datei
			hinweisen.
		</span>
	</label>
	<input type="text" size="40" autocomplete="off"
		id="longid" name="longid" value="<?= $Application?->longid ?>"
		minlength="9" maxlength="60" pattern="^[a-z0-9-]*$" required>

<?php } else { ?>

	<label for="id">
		<span class="name">ID</span>
	</label>
	<input type="text" id="id" name="id" value="<?= $Application?->id ?>" size="8" readonly>

	<label for="longid">
		<span class="name">Dateiname/Long-ID</span>
	</label>
	<input type="text" id="longid" name="longid" value="<?= $Application?->longid ?>" size="40" readonly>

	<a href="<?= $Application->src() ?>">Datei: <?= $Application->longid.'.'.$Application->extension ?></a>

<?php } ?>

	<!-- DESCRIPTION -->
	<label for="description">
		<span class="name">Beschreibung</span>
		<span class="conditions">optional, bis zu 250 Zeichen</span>
		<span class="infos">
			Die Beschreibung wird als Alternativtext angezeigt, wenn das Bild nicht geladen
			werden kann. Sie sollte den Bildinhalt wiedergeben.
		</span>
	</label>
	<input type="text" size="60"
		id="description" name="description" value="<?= $Application?->description ?>"
		maxlength="250">

	<!-- COPYRIGHT -->
	<label for="copyright">
		<span class="name">Urheberrechtshinweis</span>
		<span class="conditions">optional, bis zu 250 Zeichen</span>
		<span class="infos">
			Der Urbeherrechtshinweis kann genutzt werden, um Lizensierungsinformationen zu dem Bild
			zur Verfügung zu stellen. Er wird normalerweise unterhalb des Bildes angezeigt.
		</span>
	</label>
	<input type="text" size="50"
		id="copyright" name="copyright" value="<?= $Application?->copyright ?>"
		maxlength="250">

	<!-- ALTERNATIVE -->
	<label for="copyright">
		<span class="name">Alternativtext</span>
		<span class="conditions">optional, bis zu 250 Zeichen</span>
		<span class="infos">
			Der Alternativtext wird angezeigt, wenn das Bild nicht geladen werden kann oder der
			Benutzer einen Screenreader nutzt. Er sollte den Bildinhalt möglichst genau wiedergeben.
		</span>
	</label>
	<input type="text" size="50"
		id="alternative" name="alternative" value="<?= $Application?->alternative ?>"
		maxlength="250">


<?php if($ApplicationController->request->action == 'new'){ // TODO TODO TODO see in backend how invalid image requests are handled ?>

	<!-- IMAGEFILE -->
	<label for="file">
		<span class="name">Datei</span>
		<span class="conditions">erforderlich; PNG, JPEG oder GIF</span>
	</label>
	<input type="file" class="file"
		id="file" name="file" required>

<?php } ?>

	<button type="submit" class="green">Speichern</button>
</form>