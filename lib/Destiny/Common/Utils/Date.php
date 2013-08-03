<?php
namespace Destiny\Common\Utils;

/**
 * This class is bad
 */
abstract class Date {
	
	const STRING_FORMAT_YEAR = 'g:ia, D jS F Y e';
	const STRING_FORMAT = 'D jS F, g:ia e';
	const STRING_DATE_FORMAT = 'jS F, Y';
	const STRING_TIME_FORMAT = 'H:i';
	const FORMAT = DATE_ISO8601;

	/**
	 * Get a DateTime object
	 *
	 * @param string|int $string
	 * @return \DateTime
	 */
	public static function getDateTime($time = 'NOW') {
		if (! is_numeric ( $time )) {
			$date = new \DateTime ( $time );
		} else {
			$date = new \DateTime ();
			$date->setTimestamp ( $time );
		}
		$date->setTimezone ( new \DateTimeZone ( 'UTC' ) );
		return $date;
	}

	/**
	 * A sweet interval formatting, will use the two biggest interval parts.
	 * On small intervals, you get minutes and seconds.
	 * On big intervals, you get months and days.
	 * Only the two biggest parts are used.
	 *
	 * @param DateTime $start
	 * @param DateTime|null $end
	 * @return string
	 */
	public static function getRemainingTime($start, $end = null) {
		if (! ($start instanceof \DateTime)) {
			$start = new \DateTime ( $start );
		}
		if ($end === null) {
			$end = new \DateTime ();
		}
		if (! ($end instanceof \DateTime)) {
			$end = new \DateTime ( $start );
		}
		$interval = $end->diff ( $start );
		$doPlural = function ($nb, $str) {
			return $nb > 1 ? $str . 's' : $str;
		}; // adds plurals
		
		$format = array ();
		if ($interval->y !== 0) {
			$format [] = "%y " . $doPlural ( $interval->y, "year" );
		}
		if ($interval->m !== 0) {
			$format [] = "%m " . $doPlural ( $interval->m, "month" );
		}
		if ($interval->d !== 0) {
			$format [] = "%d " . $doPlural ( $interval->d, "day" );
		}
		if ($interval->h !== 0) {
			$format [] = "%h " . $doPlural ( $interval->h, "hour" );
		}
		if ($interval->i !== 0) {
			$format [] = "%i " . $doPlural ( $interval->i, "minute" );
		}
		if ($interval->s !== 0) {
			$format [] = "%s " . $doPlural ( $interval->s, "second" );
		}
		// We use the two biggest parts
		if (count ( $format ) > 1) {
			$format = array_shift ( $format ) . " and " . array_shift ( $format );
		} else {
			$format = array_pop ( $format );
		}
		// Prepend 'since ' or whatever you like
		return (($start < $end) ? '-' : '') . $interval->format ( $format );
	}

	/**
	 *
	 * @param \DateTime $date
	 * @param \DateTime $compareTo
	 * @return string
	 */
	public static function getElapsedTime(\DateTime $date, \DateTime $compareTo = NULL) {
		if (is_null ( $compareTo )) {
			$compareTo = new \DateTime ( 'now' );
		}
		$diff = $compareTo->format ( 'U' ) - $date->format ( 'U' );
		$dayDiff = floor ( $diff / 86400 );
		if (is_nan ( $dayDiff ) || $dayDiff < 0) {
			return '';
		}
		if ($dayDiff == 0) {
			if ($diff < 60) {
				return 'Just now';
			} elseif ($diff < 120) {
				return '1 minute ago';
			} elseif ($diff < 3600) {
				return floor ( $diff / 60 ) . ' minutes ago';
			} elseif ($diff < 7200) {
				return '1 hour ago';
			} elseif ($diff < 86400) {
				return floor ( $diff / 3600 ) . ' hours ago';
			}
		} elseif ($dayDiff == 1) {
			return 'Yesterday';
		} elseif ($dayDiff < 7) {
			return $dayDiff . ' days ago';
		} elseif ($dayDiff == 7) {
			return '1 week ago';
		} elseif ($dayDiff < (7 * 6)) {
			return ceil ( $dayDiff / 7 ) . ' weeks ago';
		} elseif ($dayDiff < 365) {
			return ceil ( $dayDiff / (365 / 12) ) . ' months ago';
		} else {
			$years = round ( $dayDiff / 365 );
			return $years . ' year' . ($years != 1 ? 's' : '') . ' ago';
		}
	}

}