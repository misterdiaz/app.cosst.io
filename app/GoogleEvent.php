<?php

namespace App;

use DateTime;
use Carbon\Carbon;
use Google_Service_Calendar_Event;
use Illuminate\Support\Collection;
use Google_Service_Calendar_EventDateTime;

class GoogleEvent
{
    /** @var \Google_Service_Calendar_Event */
    public $googleEvent;

    /** @var int */
    protected $calendarId;

    protected $attendees;

    public static function createFromGoogleCalendarEvent(Google_Service_Calendar_Event $googleEvent, $calendarId="primary")
    {
        $event = new static();

        $event->googleEvent = $googleEvent;

        $event->calendarId = $calendarId;

        return $event;
    }

    public static function create(array $properties, string $calendarId = null)
    {
        $event = new static();

        $event->calendarId = static::getGoogleCalendar($calendarId)->getCalendarId();

        foreach ($properties as $name => $value) {
            $event->$name = $value;
        }

        return $event->save('insertEvent');
    }

    public function __construct()
    {
        $this->attendees = [];
        $this->googleEvent = new Google_Service_Calendar_Event();
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        $name = $this->getFieldName($name);

        if ($name === 'sortDate') {
            return $this->getSortDate();
        }

        $value = array_get($this->googleEvent, $name);

        if (in_array($name, ['start.date', 'end.date']) && $value) {
            $value = Carbon::createFromFormat('Y-m-d', $value)->startOfDay();
        }

        if (in_array($name, ['start.dateTime', 'end.dateTime']) && $value) {
            $value = Carbon::createFromFormat(DateTime::RFC3339, $value);
        }

        return $value;
    }

    public function __set($name, $value)
    {
        $name = $this->getFieldName($name);

        if (in_array($name, ['start.date', 'end.date', 'start.dateTime', 'end.dateTime'])) {
            $this->setDateProperty($name, $value);

            return;
        }

        array_set($this->googleEvent, $name, $value);
    }

    public function exists(): bool
    {
        return $this->id != '';
    }

    public function isAllDayEvent(): bool
    {
        return is_null($this->googleEvent['start']['dateTime']);
    }

    /**
     * @param \Carbon\Carbon|null $startDateTime
     * @param \Carbon\Carbon|null $endDateTime
     * @param array               $queryParameters
     * @param string|null         $calendarId
     *
     * @return \Illuminate\Support\Collection
     */
    public static function get(
        Carbon $startDateTime = null,
        Carbon $endDateTime = null,
        array $queryParameters = [],
        string $calendarId = null
    ): Collection {
        $googleCalendar = static::getGoogleCalendar($calendarId);

        $googleEvents = $googleCalendar->listEvents($startDateTime, $endDateTime, $queryParameters);

        return collect($googleEvents)
            ->map(function (Google_Service_Calendar_Event $event) use ($calendarId) {
                return static::createFromGoogleCalendarEvent($event, $calendarId);
            })
            ->sortBy(function (Event $event) {
                return $event->sortDate;
            })
            ->values();
    }

    /**
     * @param string $eventId
     * @param string $calendarId
     *
     * @return \Codegis\GoogleCalendar\Event
     */
    public static function find($eventId, $calendarId = "primary"): GoogleEvent
    {
        $googleCalendar = static::getGoogleCalendar($calendarId);

        $googleEvent = $googleCalendar->getEvent($eventId);

        return static::createFromGoogleCalendarEvent($googleEvent, $calendarId);
    }

    public function save($agent_id = null, $method = null): GoogleEvent
    {
        $method = $method ?? ($this->exists() ? 'updateEvent' : 'insertEvent');

        $googleCalendar = $this->getGoogleCalendar($this->calendarId, $agent_id);

        $this->googleEvent->setAttendees($this->attendees);

        $googleEvent = $googleCalendar->$method($this);

        return static::createFromGoogleCalendarEvent($googleEvent, $googleCalendar->getCalendarId());
    }

    /**
     * @param string $eventId
     */
    public function delete(string $eventId = null)
    {
        $this->getGoogleCalendar($this->calendarId)->deleteEvent($eventId ?? $this->id);
    }

    /**
     * @param string $calendarId
     *
     * @return \Codegis\GoogleCalendar\GoogleCalendar
     */
    protected static function getGoogleCalendar($calendarId = null, $agent_id = null)
    {
        $google = new \App\GoogleClient($agent_id);
        $service = new \Google_Service_Calendar( $google->getClient() );
        $Calendar = new \App\GoogleCalendar($service);

        return $Calendar;
    }

    /**
     * @param string         $name
     * @param \Carbon\Carbon $date
     */
    protected function setDateProperty(string $name, Carbon $date)
    {
        $eventDateTime = new Google_Service_Calendar_EventDateTime();

        if (in_array($name, ['start.date', 'end.date'])) {
            $eventDateTime->setDate($date->format('Y-m-d'));
            $eventDateTime->setTimezone($date->getTimezone());
        }

        if (in_array($name, ['start.dateTime', 'end.dateTime'])) {
            $eventDateTime->setDateTime($date->format(DateTime::RFC3339));
            $eventDateTime->setTimezone($date->getTimezone());
        }

        if (starts_with($name, 'start')) {
            $this->googleEvent->setStart($eventDateTime);
        }

        if (starts_with($name, 'end')) {
            $this->googleEvent->setEnd($eventDateTime);
        }
    }

    public function addAttendee(array $attendees)
    {
        $this->attendees[] = $attendees;
    }

    protected function getFieldName(string $name): string
    {
        return [
            'name'          => 'summary',
            'description'   => 'description',
            'startDate'     => 'start.date',
            'endDate'       => 'end.date',
            'startDateTime' => 'start.dateTime',
            'endDateTime'   => 'end.dateTime',
            'status'        => 'status',
            'id'            => 'id',
        ][$name] ?? $name;
    }

    public function getSortDate(): string
    {
        if ($this->startDate) {
            return $this->startDate;
        }

        if ($this->startDateTime) {
            return $this->startDateTime;
        }

        return '';
    }

    public function getId(){
        return array_get($this->googleEvent, "id");
    }
}