<?php

namespace Drupal\surf_migrate\Plugin\migrate\process;

use Drupal\Component\Datetime\DateTimePlus;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\migrate\MigrateException;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Plugin\migrate\process\FormatDate;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Converts date/datetime into a smart date value.
 *
 * Available configuration keys
 * - from_format: The source format string as accepted by
 *   @link http://php.net/manual/datetime.createfromformat.php \DateTime::createFromFormat. @endlink
 * - from_timezone: String identifying the required source time zone, see
 *   DateTimePlus::__construct().
 * - settings: keyed array of settings, see DateTimePlus::__construct().
 * - allday: Force datetimes to be expressed as one single day.
 *
 * @MigrateProcessPlugin(
 *   id = "smart_date"
  * )
 */
class SmartDate extends ProcessPluginBase {


  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (empty($value) && $value !== '0' && $value !== 0) {
      // This was also changed from the FormatDate class.
      // In the case of NullCoalesce this needs to actually be NULL.
      return $value;
    }

    // Validate the configuration.
    if (empty($this->configuration['from_format'])) {
      throw new MigrateException('Format date plugin is missing from_format configuration.');
    }

    $fromFormat = $this->configuration['from_format'];
    $toFormat = 'U';
    $system_timezone = date_default_timezone_get();
    $default_timezone = !empty($system_timezone) ? $system_timezone : 'UTC';
    $from_timezone = $this->configuration['from_timezone'] ?? $default_timezone;
    $to_timezone = $this->configuration['to_timezone'] ?? $default_timezone;
    $allday = $this->configuration['allday'] ?? FALSE;
    $settings = $this->configuration['settings'] ?? [];

    // Older versions of Drupal where omitting certain granularity values (also
    // known as "collected date attributes") resulted in invalid timestamps
    // getting stored.
    if ($fromFormat === 'Y-m-d\TH:i:s') {
      $value = str_replace(['-00-00T', '-00T'], ['-01-01T', '-01T'], $value);
    }

    // Attempts to transform the supplied date using the defined input format.
    // DateTimePlus::createFromFormat can throw exceptions, so we need to
    // explicitly check for problems.
    try {
      $date = DateTimePlus::createFromFormat($fromFormat, $value, $from_timezone, $settings);
      if ($allday === 'auto') {
        $this->detectAllDay($allday, $date, $destination_property);
      }
      if ($allday) {
        $this->convertToAllDayTimestamp($date, $destination_property);
      }
      $transformed = $date->format($toFormat, ['timezone' => $to_timezone]);
    }
    catch (\InvalidArgumentException $e) {
      throw new MigrateException(sprintf("Format date plugin could not transform '%s' using the format '%s'. Error: %s", $value, $fromFormat, $e->getMessage()), $e->getCode(), $e);
    }
    catch (\UnexpectedValueException $e) {
      throw new MigrateException(sprintf("Format date plugin could not transform '%s' using the format '%s'. Error: %s", $value, $fromFormat, $e->getMessage()), $e->getCode(), $e);
    }

    return $transformed;
  }

  private function detectAllDay(&$allday_setting, DateTimePlus $date, $destination_property) {
    $time = $date->format('H:i:s');
    if ($time === '00:00:00') {
      $allday_setting = 'allday';
    }
    else {
      $allday_setting = FALSE;
    }
  }

  private function convertToAllDayTimestamp(DateTimePlus $date, $destination_property) {
    $props = explode('/', $destination_property);
    $prop = array_pop($props);
    if ($prop === 'value') {
      $date->setTime(0,0,0);
    }
    if ($prop === 'end_value') {
      $date->setTime(23, 59, 0);
    }
  }
}
