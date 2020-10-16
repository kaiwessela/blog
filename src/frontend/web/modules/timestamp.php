<?php
namespace Blog\Frontend\Web\Modules;

class Timestamp {
	public $unix;

	const DATE_SHORT = '%d.%m.%Y'; # 09.01.2020
	const DATE = '%e.&nbsp;%B&nbsp;%Y'; # 9. Januar 2020
	const DATE_LONG = '%A,&nbsp;%e.&nbsp;%B&nbsp;%Y'; # Montag, 9. Januar 2020
	const DATETIME_SHORT = '%d.%m.%Y,&nbsp;%k.%M&nbsp;Uhr'; # 09.01.2020, 7.45 Uhr
	const DATETIME = '%e.&nbsp;%B&nbsp;%Y,&nbsp;%k.%M&nbsp;Uhr'; # 9. Januar 2020, 7.45 Uhr
	const DATETIME_LONG = '%A,&nbsp;%e.&nbsp;%B&nbsp;%Y,&nbsp;%k.%M&nbsp;Uhr'; # Montag, 9. Januar 2020, 7.45 Uhr
	const TIME = '%k.%M&nbsp;Uhr'; # 7.45 Uhr


	function __construct(int $unix) {
		$this->unix = $unix;
	}

	function __toString() {
		return $this->unix;
	}

	function __get($name) {
		switch($name){
			case 'date_short': 		return $this->format(Timestamp::DATE_SHORT);
			case 'date': 			return $this->format(Timestamp::DATE);
			case 'date_long': 		return $this->format(Timestamp::DATE_LONG);
			case 'datetime_short': 	return $this->format(Timestamp::DATETIME_SHORT);
			case 'datetime': 		return $this->format(Timestamp::DATETIME);
			case 'datetime_long': 	return $this->format(Timestamp::DATETIME_LONG);
			case 'time': 			return $this->format(Timestamp::TIME);
			case 'iso': 			return $this->iso();
			case 'rfc2822': 		return $this->rfc2822();
			default: break;
		}
	}

	public function unix() {
		return $this->unix;
	}

	public function iso() {
		return date('c', $this->unix);
	}

	public function rfc2822() {
		return date('r', $this->unix);
	}

	public function format($pattern = self::DATETIME) {
		return strftime($pattern, $this->unix);
	}

	public static function now() {
		return new Timestamp(time());
	}
}
?>
