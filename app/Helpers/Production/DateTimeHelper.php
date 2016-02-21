<?php namespace App\Helpers\Production;

use App\Helpers\DateTimeHelperInterface;
use \Carbon\Carbon;

class DateTimeHelper implements DateTimeHelperInterface
{

    const PRESENTATION_TIME_ZONE_SESSION_KEY = 'presentation-time-zone';

    public function timezoneForStorage()
    {
        return new \DateTimeZone(\Config::get('app.timezone'));
    }

    public function setPresentationTimeZone($timezone = null)
    {
        \Session::set(static::PRESENTATION_TIME_ZONE_SESSION_KEY,$timezone);
    }

    public function clearPresentationTimeZone()
    {
        \Session::remove(static::PRESENTATION_TIME_ZONE_SESSION_KEY);
    }

    public function getPresentationTimeZoneString()
    {
        $timezone = \Session::get(static::PRESENTATION_TIME_ZONE_SESSION_KEY);
        if( empty($timezone) ) {
            $timezone = \Config::get('app.default_presentation_timezone');
        }
        return $timezone;
    }

    public function timezoneForPresentation()
    {
        return new \DateTimeZone($this->getPresentationTimeZoneString());
    }

    public function now(\DateTimeZone $timezone = null)
    {
        $timezone = empty($timezone) ? $this->timezoneForStorage() : $timezone;

        return Carbon::now($timezone);
    }

    public function dateTime($dateTimeStr, \DateTimeZone $timezoneFrom = null, \DateTimeZone $timezoneTo = null)
    {
        $timezoneFrom = empty( $timezoneFrom ) ? $this->timezoneForPresentation() : $timezoneFrom;
        $timezoneTo   = empty( $timezoneTo ) ? $this->timezoneForStorage() : $timezoneTo;

        return Carbon::parse($dateTimeStr, $timezoneFrom)->setTimezone($timezoneTo);
    }

    public function formatDate($dateTime, \DateTimeZone $timezone = null)
    {
        $viewDateTime = clone $dateTime;
        $timezone = empty($timezone) ? $this->timezoneForPresentation() : $timezone;
        $viewDateTime->setTimeZone($timezone);

        return $viewDateTime->format('Y-m-d');
    }

    public function formatTime($dateTime, \DateTimeZone $timezone = null)
    {
        $viewDateTime = clone $dateTime;
        $timezone = empty($timezone) ? $this->timezoneForPresentation() : $timezone;
        $viewDateTime->setTimeZone($timezone);

        return $viewDateTime->format('H:i');
    }

    public function formatDateTime($dateTime, $format = "Y-m-d H:i", \DateTimeZone $timezone = null)
    {
        if (empty($dateTime)) {
            $dateTime = $this->now();
        }
        $viewDateTime = clone $dateTime;
        $timezone = empty($timezone) ? $this->timezoneForPresentation() : $timezone;
        $viewDateTime->setTimeZone($timezone);

        return $viewDateTime->format($format);
    }


}