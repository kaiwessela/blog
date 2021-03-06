<?php use \Blog\Config\MediaConfig; ?>

<form action="#" method="post" class="posts edit">

<?php if($Controller->call->action == 'new'){ ?>

	<!-- LONGID -->
	<label for="longid">
		<span class="name">Post-ID</span>
		<span class="conditions">
			erforderlich; 9 bis 60 Zeichen, nur Kleinbuchstaben (a-z), Ziffern (0-9) und
			Bindestriche (-)
		</span>
		<span class="infos">
			Die Post-ID wird in der URL verwendet und entspricht oftmals ungefähr der Überschrift.
		</span>
	</label>
	<input type="text" size="60" autocomplete="off"
		id="longid" name="longid" value="<?= $Object?->longid ?>"
		minlength="9" maxlength="60" pattern="^[a-z0-9-]*$" required>

<?php } else { ?>

	<label for="id">
		<span class="name">ID</span>
	</label>
	<input type="text" id="id" name="id" value="<?= $Object?->id ?>" size="8" readonly>

	<label for="longid">
		<span class="name">Long-ID</span>
	</label>
	<input type="text" id="longid" name="longid" value="<?= $Object?->longid ?>" size="60" readonly>

<?php } ?>

	<!-- OVERLINE -->
	<label for="overline">
		<span class="name">Dachzeile</span>
		<span class="conditions">optional, bis zu 50 Zeichen</span>
		<span class="infos">
			Die Dachzeile steht direkt über der Überschrift und beinhaltet meist Stichwörter,
			die auf das Thema des Artikels hinweisen.
		</span>
	</label>
	<input type="text" size="50"
		id="overline" name="overline" value="<?= $Object?->overline ?>"
		maxlength="50">

	<!-- HEADLINE -->
	<label for="headline">
		<span class="name">Schlagzeile</span>
		<span class="conditions">erforderlich, 1 bis 100 Zeichen</span>
		<span class="infos">
			Die Schlagzeile ist die Überschrift des Artikels und fasst die Kernaussage prägnant
			zusammen.
		</span>
	</label>
	<input type="text" size="100"
		id="headline" name="headline" value="<?= $Object?->headline ?>"
		maxlength="100" required>

	<!-- SUBLINE -->
	<label for="subline">
		<span class="name">Unterzeile</span>
		<span class="conditions">optional, bis zu 100 Zeichen</span>
		<span class="infos">
			Die Unterzeile steht unterhalb der Schlagzeile und ergänzt diese um weitere
			Informationen.
		</span>
	</label>
	<input type="text" size="100"
		id="subline" name="subline" value="<?= $Object?->subline ?>"
		maxlength="100">

	<!-- TEASER -->
	<label for="teaser">
		<span class="name">Teaser</span>
		<span class="conditions">optional</span>
		<span class="infos">
			Eine kurze Zusammenfassung des Artikels, die in der Vorschau angezeigt wird.
		</span>
	</label>
	<textarea id="teaser" name="teaser"
		cols="80" rows="5"><?= $Object?->teaser ?></textarea>

	<!-- AUTHOR -->
	<label for="author">
		<span class="name">Autor</span>
		<span class="conditions">erforderlich, 1 bis 100 Zeichen</span>
		<span class="infos">Der Autor oder die Autoren des Artikels.</span>
	</label>
	<input type="text" size="100"
		id="author" name="author" value="<?= $Object?->author ?>"
		maxlength="100" required>

	<!-- TIMESTAMP -->
	<label for="timestamp">
		<span class="name">Veröffentlichungsdatum und -uhrzeit</span>
		<span class="conditions">erforderlich</span>
		<span class="infos">
			Datum und Uhrzeit der Veröffentlichung. Hat derzeit nur eine informierende Funktion,
			Artikel mit Datum in der Zukunft werden trotzdem angezeigt. Es ist aber eine Funktion
			zur terminierten Veröffentlichung geplant.
		</span>
	</label>
	<input type="text" size="19"
		id="timestamp" name="timestamp" value="<?= $Object?->timestamp ?>"
		required>

	<!-- IMAGE -->
	<label for="image_id">
		<span class="name">Artikelbild</span>
		<span class="conditions">optional</span>
		<span class="infos">
			Das Hauptbild des Artikels. Wird in der Vorschau und zwischen Titel und Inhalt
			dargestellt.
		</span>
	</label>
	<input type="text" class="imageinput" size="8"
		id="image_id" name="image_id" value="<?= $Object?->image?->id ?>"
		minlength="8" maxlength="8">

	<!-- CONTENT -->
	<label for="content">
		<span class="name">Inhalt</span>
		<span class="conditions">
			optional, Markdown-Schreibweise möglich
			(<a href="https://de.wikipedia.org/wiki/Markdown">Wikipedia: Markdown</a>)
		</span>
		<span class="infos">Der eigentliche Inhalt des Artikels</span>
	</label>
	<textarea id="content" name="content"
		cols="100" rows="40"><?= $Object?->content ?></textarea>

	<!-- COLUMNS -->
	<label>
		<span class="name">Rubriken</span>
		<span class="conditions">optional, Mehrfacheintrag nicht erlaubt</span>
		<span class="infos">
			Änderungen werden lokal zwischengespeichert und beim Abschicken übernommen.
		</span>
	</label>
	<div class="relationinput nojs" data-type="Column" data-unique="true" data-for="columns" data-selectmodal="columns-select">
		<div class="objects">
			<?php $Object?->columnrelations?->foreach(function($i, $rel){ ?>
				<div class="relation" data-i="<?= $i ?>" data-exists="true">
					<input type="hidden" name="columnrelations[<?= $i ?>][id]" value="<?= $rel->id ?>">
					<input type="hidden" name="columnrelations[<?= $i ?>][action]" class="action" value="ignore">
					<input type="hidden" name="columnrelations[<?= $i ?>][column_id]" class="objectId" value="<?= $rel->column->id ?>">
					<input type="hidden" name="columnrelations[<?= $i ?>][post_id]" value="<?= $Object?->id ?>">
					<p class="title"><span><?= $rel->column->name ?></span> – <code><?= $rel->column->longid ?></code></p>
					<button type="button" class="red" data-action="remove">Entfernen</button>
					<button type="button" data-action="restore">Entf. rückgängig</button>
				</div>
			<?php }); ?>
			<template>
				<div class="relation" data-i="{{i}}" data-exists="false">
					<input type="hidden" name="columnrelations[{{i}}][action]" class="action" value="new">
					<input type="hidden" name="columnrelations[{{i}}][column_id]" class="objectId" value="{{id}}">
					<input type="hidden" name="columnrelations[{{i}}][post_id]" value="<?= $Object?->id ?>">
					<p class="title"><span>{{name}}</span> – <code>{{longid}}</code></p>
					<button type="button" class="red" data-action="remove">Entfernen</button>
				</div>
			</template>
		</div>
		<button type="button" class="new blue" data-action="select">Rubrik(en) hinzufügen</button>
	</div>


	<button type="submit" class="green">Speichern</button>
</form>

<div class="modal selectmodal nojs" data-name="image-select" data-type="Image" data-objectsperpage="20">
	<div class="box">
		<h2>Bild auswählen</h2>
		<div class="pagination">
			<template>
				<button type="button" data-action="paginate" data-page="{{page}}">{{page}}</button>
			</template>
		</div>
		<form action="#" method="GET">
			<section class="objects">
				<template>
					<article>
						<label>
							<input type="radio" name="result" value="{{id}}" {{current}}>
							<img src="<?= $server->url ?>/media/images/{{longid}}/{{longid}}.{{extension}}">
						</label>
					</article>
				</template>
			</section>
			<button type="submit" data-action="submit" class="blue">Auswählen</button>
			<button type="button" data-action="close" class="red">Schließen</button>
		</form>
	</div>
</div>

<div class="modal uploadmodal nojs" data-name="image-upload" data-type="Image">
	<div class="box">
		<h2>Neues Bild hochladen</h2>
		<form action="#" method="GET">
			<label for="image-upload-longid">
				<span class="name">Bild-ID</span>
				<span class="conditions">
					erforderlich; 9 bis 60 Zeichen, nur Kleinbuchstaben (a-z), Ziffern (0-9) und
					Bindestriche (-)
				</span>
				<span class="infos">
					Die Bild-ID wird in der URL verwendet und sollte auf den Titel oder Inhalt
					hinweisen.
				</span>
			</label>
			<input type="text" size="40" autocomplete="off"
				id="image-upload-longid" name="longid"
				minlength="9" maxlength="60" pattern="^[a-z0-9-]*$" required>

			<label for="image-upload-title">
				<span class="name">Titel</span>
				<span class="conditions">optional, bis zu 60 Zeichen</span>
				<span class="infos">Der Titel des Bildes.</span>
			</label>
			<input type="text" size="40"
				id="image-upload-title" name="title"
				maxlength="60">

			<label for="image-upload-description">
				<span class="name">Beschreibung</span>
				<span class="conditions">optional, bis zu 250 Zeichen</span>
				<span class="infos">
					Eine kurze Beschreibung des Bildes.
				</span>
			</label>
			<input type="text" size="80"
				id="image-upload-description" name="description"
				maxlength="250">

			<label for="image-upload-copyright">
				<span class="name">Urheberrechtshinweis</span>
				<span class="conditions">optional, bis zu 250 Zeichen</span>
				<span class="infos">
					Der Urbeherrechtshinweis kann genutzt werden, um den Urheber des Bildes und die Lizenz,
					unter der es zur Verfügung steht, anzugeben.
				</span>
			</label>
			<input type="text" size="80"
				id="image-upload-copyright" name="copyright"
				maxlength="250">

			<label for="image-upload-alternative">
				<span class="name">Alternativtext</span>
				<span class="conditions">optional, bis zu 250 Zeichen</span>
				<span class="infos">
					Der Alternativtext wird angezeigt, wenn das Bild nicht geladen werden kann oder der
					Benutzer einen Screenreader nutzt. Er sollte den Bildinhalt wiedergeben.
				</span>
			</label>
			<input type="text" size="80"
				id="image-upload-alternative" name="alternative"
				maxlength="250">

			<label for="image-upload-file">
				<span class="name">Datei</span>
				<span class="conditions">erforderlich</span>
			</label>
			<input type="file" class="file"
				id="image-upload-file" name="file" required
				accept="<?= implode(', ', MediaConfig::IMAGE_TYPES); ?>">

			<button type="submit" data-action="submit" class="green">Hochladen</button>
			<button type="button" data-action="close" class="red">Schließen</button>
		</form>
	</div>
</div>

<div class="pseudoinput nojs" data-type="Image" data-for="image_id" data-selectmodal="image-select" data-uploadmodal="image-upload">
	<div class="object"></div>
	<template data-state="empty">
		<p>Kein Bild ausgewählt.</p>
	</template>
	<template data-state="set">
		<figure>
			<img src="<?= $server->url ?>/media/images/{{longid}}/{{longid}}.{{extension}}" alt="{{alternative}}">
			<figcaption>{{longid}}</figcaption>
		</figure>
	</template>
	<button type="button" class="blue" data-action="select">Aus vorhandenen Bildern auswählen</button>
	<button type="button" class="green" data-action="upload">Neues Bild hochladen</button>
	<button type="button" class="red" data-action="clear">Bild entfernen</button>
</div>

<div class="timeinput nojs" data-for="timestamp">
	<label>
		Datum:
		<input type="date">
	</label>
	<label>
		Uhrzeit:
		<input type="time">
	</label>
</div>

<div class="modal multiselectmodal nojs" data-name="columns-select" data-type="Column" data-objectsperpage="20">
	<div class="box">
		<h2>Rubriken auswählen</h2>
		<form action="#" method="GET">
			<section class="objects">
				<template>
					<article>
						<label class="checkbodge turn-around">
							<span class="label-field">{{name}} – {{longid}}</span>
							<input type="checkbox" name="result" value="{{id}}">
							<span class="bodgecheckbox">
								<span class="bodgetick">
									<span class="bodgetick-down"></span>
									<span class="bodgetick-up"></span>
								</span>
							</span>
						</label>
					</article>
				</template>
			</section>
			<button type="button" data-action="loadmore">Weitere Rubriken laden</button><br>
			<button type="submit" data-action="submit" class="blue">Auswählen</button>
			<button type="button" data-action="close" class="red">Schließen</button>
		</form>
	</div>
</div>
